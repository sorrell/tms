<?php

namespace App\Traits;

use App\Models\Contact;
use App\Models\Documents\Document;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Models\Audit;

trait HandlesAuditHistory
{
    protected function getAuditHistory(Model & Auditable $parentModel): \Illuminate\Support\Collection
    {
        $parentClass = get_class($parentModel);
        
        // Get parent model audits
        $parentAudits = $parentModel->audits()->with('user')->get();

        // Get document audits for documents belonging to this parent model
        $documentAudits = $this->getRelatedModelAudits(
            Document::class,
            $parentClass,
            $parentModel->getKey(),
            'documentable_type',
            'documentable_id'
        );

        // Get contact audits for contacts belonging to this parent model
        $contactAudits = $this->getRelatedModelAudits(
            Contact::class,
            $parentClass,
            $parentModel->getKey(),
            'contact_for_type',
            'contact_for_id'
        );

        // Merge all audits and sort by created_at desc
        return $parentAudits->merge($documentAudits)->merge($contactAudits)
            ->sortByDesc('created_at')
            ->values();
    }

    private function getRelatedModelAudits(
        string $auditableType,
        string $parentClass,
        mixed $parentId,
        string $typeField,
        string $idField
    ): \Illuminate\Support\Collection {
        return Audit::where('auditable_type', $auditableType)
            ->where(function ($query) use ($parentClass, $parentId, $typeField, $idField) {
                // Include audits where the model still exists
                $query->whereHas('auditable', function ($subQuery) use ($parentClass, $parentId, $typeField, $idField) {
                    $subQuery->where($typeField, $parentClass)
                             ->where($idField, $parentId);
                })
                // OR include audits where model was deleted but belonged to this parent
                ->orWhere(function ($subQuery) use ($parentClass, $parentId, $typeField, $idField) {
                    $subQuery->whereDoesntHave('auditable')
                             ->where(function ($valueQuery) use ($parentClass, $parentId, $typeField, $idField) {
                                 $valueQuery->whereJsonContains("old_values->{$typeField}", $parentClass)
                                           ->whereJsonContains("old_values->{$idField}", $parentId)
                                           ->orWhereJsonContains("new_values->{$typeField}", $parentClass)
                                           ->whereJsonContains("new_values->{$idField}", $parentId);
                             });
                });
            })
            ->with('user', 'auditable')
            ->get();
    }

    protected function formatAuditData(\Illuminate\Support\Collection $audits): \Illuminate\Support\Collection
    {
        return $audits->map(function (Audit $audit) {
            /** @var mixed $auditableType */
            $auditableType = $audit->getAttributeValue('auditable_type');
            /** @var mixed $oldValues */
            $oldValues = $audit->getAttributeValue('old_values') ?? [];
            /** @var mixed $newValues */
            $newValues = $audit->getAttributeValue('new_values') ?? [];
            
            return [
                'id' => $audit->getKey(),
                'event' => $audit->getAttributeValue('event'),
                'auditable_type' => $auditableType,
                'auditable_id' => $audit->getAttributeValue('auditable_id'),
                'entity_type' => $this->getEntityType($auditableType),
                'entity_name' => $this->getEntityName($audit),
                'user' => $audit->user ? [
                    'id' => $audit->user->getKey(),
                    'name' => $audit->user->getAttributeValue('name'),
                    'email' => $audit->user->getAttributeValue('email'),
                ] : null,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'url' => $audit->getAttributeValue('url'),
                'ip_address' => $audit->getAttributeValue('ip_address'),
                'user_agent' => $audit->getAttributeValue('user_agent'),
                'created_at' => $audit->getAttributeValue('created_at'),
                'created_at_human' => $audit->getAttributeValue('created_at')?->diffForHumans(),
                'changes' => $this->formatChanges($oldValues, $newValues, $auditableType),
            ];
        });
    }

    private function formatChanges(array $oldValues, array $newValues, string $auditableType): array
    {
        $changes = [];
        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
        
        // Filter out system fields we don't want to show in the UI
        $systemFields = [
            'id',
            'organization_id',
            'contact_for_id',
            'contact_for_type',
            'documentable_id',
            'documentable_type',
            'created_at',
            'updated_at'
        ];

        foreach ($allKeys as $key) {
            // Skip system fields
            if (in_array($key, $systemFields)) {
                continue;
            }
            
            $oldValue = $oldValues[$key] ?? null;
            $newValue = $newValues[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changes[] = [
                    'field' => $this->formatFieldName($key, $auditableType),
                    'field_name' => $key,
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ];
            }
        }

        return $changes;
    }

    private function formatFieldName(string $field, string $auditableType): string
    {
        // Get the specific field mappings for the parent model
        $specificMappings = $this->getModelSpecificFieldMappings($auditableType);
        
        if (isset($specificMappings[$field])) {
            return $specificMappings[$field];
        }

        // Handle document-specific fields
        if ($auditableType === Document::class) {
            return match ($field) {
                'uploaded_by' => 'Uploaded By',
                'folder_name' => 'Folder Name',
                default => ucwords(str_replace('_', ' ', $field)),
            };
        }

        // Handle contact-specific fields
        if ($auditableType === Contact::class) {
            return match ($field) {
                'contact_type' => 'Contact Type',
                'mobile_phone' => 'Mobile Phone',
                'office_phone' => 'Office Phone',
                'office_phone_extension' => 'Office Phone Extension',
                default => ucwords(str_replace('_', ' ', $field)),
            };
        }

        return ucwords(str_replace('_', ' ', $field));
    }

    private function getEntityType(string $auditableType): string
    {
        return match ($auditableType) {
            \App\Models\Customers\Customer::class => 'Customer',
            \App\Models\Facility::class => 'Facility',
            \App\Models\Carriers\Carrier::class => 'Carrier',
            Document::class => 'Document',
            Contact::class => 'Contact',
            default => class_basename($auditableType),
        };
    }

    private function getEntityName(Audit $audit): string
    {
        $auditable = $audit->auditable;
        /** @var mixed $auditableType */
        $auditableType = $audit->getAttributeValue('auditable_type');
        /** @var mixed $oldValues */
        $oldValues = $audit->getAttributeValue('old_values') ?? [];
        /** @var mixed $newValues */
        $newValues = $audit->getAttributeValue('new_values') ?? [];
        
        if (!$auditable) {
            // For deleted entities, try to get name from old_values or new_values
            $name = $oldValues['name'] ?? $newValues['name'] ?? null;
            
            return match ($auditableType) {
                \App\Models\Customers\Customer::class => $name ? $name . ' (deleted)' : 'Deleted Customer',
                \App\Models\Facility::class => $name ? $name . ' (deleted)' : 'Deleted Facility',
                \App\Models\Carriers\Carrier::class => $name ? $name . ' (deleted)' : 'Deleted Carrier',
                Document::class => $name ? $name . ' (deleted)' : 'Deleted Document',
                Contact::class => $name ? $name . ' (deleted)' : 'Deleted Contact',
                default => 'Deleted Entity',
            };
        }

        return match ($auditableType) {
            \App\Models\Customers\Customer::class => $auditable->getAttributeValue('name') ?? 'Unknown Customer',
            \App\Models\Facility::class => $auditable->getAttributeValue('name') ?? 'Unknown Facility',
            \App\Models\Carriers\Carrier::class => $auditable->getAttributeValue('name') ?? 'Unknown Carrier',
            Document::class => $auditable->getAttributeValue('name') ?? 'Unknown Document',
            Contact::class => $auditable->getAttributeValue('name') ?? 'Unknown Contact',
            default => 'Unknown Entity',
        };
    }

    // Abstract method to be implemented by each audit action class
    abstract protected function getModelSpecificFieldMappings(string $auditableType): array;
}