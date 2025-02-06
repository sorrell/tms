'use client';

import { Check, ChevronsUpDown, Loader2, Plus } from 'lucide-react';
import * as React from 'react';

import { Button } from '@/Components/ui/button';
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from '@/Components/ui/command';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/Components/ui/popover';
import { cn } from '@/lib/utils';
import { CreateFormResult } from '@/types/create-form';
import axios from 'axios';
import { CommandLoading } from 'cmdk';
import { useCallback, useRef } from 'react';
import FacilityForm from './CreateForms/FacilityForm';
import { Badge } from './ui/badge';
import { Dialog, DialogContent, DialogFooter } from './ui/dialog';

export interface SelectOption {
    value: string;
    label: string;
}

export interface ApiSearchResponse {
    id: number;
    selectable_label?: string;
    name?: string;
}

export function ResourceSearchSelect({
    searchRoute,
    onValueChange,
    onValueObjectChange,
    createForm,
    defaultSelectedItems = [],
    allowMultiple = true,
    allowUnselect = true,
    autoLoadOptions = true,
    className,
}: {
    searchRoute: string;
    onValueChange?: (value: string | string[]) => void;
    onValueObjectChange?: (selected: SelectOption[] | SelectOption) => void;
    createForm?: typeof FacilityForm;
    defaultSelectedItems?: string[] | string | number[] | number;
    allowMultiple?: boolean;
    allowUnselect?: boolean;
    autoLoadOptions?: boolean;
    className?: string;
}) {
    const [newFormOpen, setNewFormOpen] = React.useState(false);
    const newFormRef = useRef<HTMLFormElement>(null);

    const [open, setOpen] = React.useState(false);
    const [selectedItems, setSelectedItems] = React.useState<SelectOption[]>(
        [],
    );
    const [search, setSearch] = React.useState('');
    const [dataOptions, setDataOptions] = React.useState<SelectOption[]>([]);
    const [loading, setLoading] = React.useState(false);
    const searchTimeout = React.useRef<NodeJS.Timeout>();

    const valuesChangedHandler = useCallback(
        (newSelected: SelectOption[]) => {
            onValueChange?.(
                allowMultiple
                    ? newSelected.map((v: SelectOption) => v.value)
                    : newSelected[0].value,
            );

            onValueObjectChange?.(allowMultiple ? newSelected : newSelected[0]);

            // If not multiple, close the popup since the user
            // selected an item
            if (!allowMultiple) {
                setOpen(false);
            }
        },
        [allowMultiple, onValueChange, onValueObjectChange],
    );

    const searchData = useCallback(
        (searchInput: string, searchIds?: string[]) => {
            setLoading(true);
            axios
                .get(searchRoute, {
                    params: {
                        query: searchInput,
                        ids: searchIds ?? null,
                    },
                })
                .then((response) => {
                    const options = response.data.map(
                        (item: ApiSearchResponse) => ({
                            value: item.id.toString(),
                            label: item.selectable_label ?? item.name,
                        }),
                    );
                    setSearch(searchInput);
                    setDataOptions(options);
                    if (searchIds) {
                        const newSelected = options.filter((o: SelectOption) =>
                            searchIds.includes(o.value),
                        );

                        setSelectedItems(newSelected);

                        valuesChangedHandler(newSelected);
                    }
                    setLoading(false);
                })
                .catch((error) => {
                    console.error('Error fetching data', error);
                    setLoading(false);
                });
        },
        [searchRoute, valuesChangedHandler],
    );

    React.useEffect(() => {
        if (defaultSelectedItems) {
            const items = (
                Array.isArray(defaultSelectedItems)
                    ? defaultSelectedItems
                    : [defaultSelectedItems]
            ).map((item: string | number) => item?.toString());

            // Check if the current selection is different from the defaultSelectedItems
            const currentSelectedIds = selectedItems.map((item) => item.value);
            const hasChanges =
                items.some(
                    (id: string | null) =>
                        !currentSelectedIds.includes(id?.toString() ?? ''),
                ) || currentSelectedIds.some((id) => !items.includes(id));

            if (hasChanges) {
                searchData('', items as string[]);
            }
        }
    }, [defaultSelectedItems, autoLoadOptions, searchData, selectedItems]);

    const debouncedSearch = (searchInput: string) => {
        setSearch(searchInput);
        setLoading(true);

        if (searchTimeout.current) {
            clearTimeout(searchTimeout.current);
        }

        searchTimeout.current = setTimeout(() => {
            searchData(searchInput);
        }, 300);
    };

    const getAllOptions = () => {
        return dataOptions
            .filter((o) => !selectedItems.find((s) => s.value === o.value))
            .concat(selectedItems)
            .sort((a, b) => {
                return a.label.localeCompare(b.label);
            });
    };

    React.useEffect(() => {
        return () => {
            if (searchTimeout.current) {
                clearTimeout(searchTimeout.current);
            }
        };
    }, []);

    return (
        <>
            <div className="flex">
                <Popover open={open} onOpenChange={setOpen}>
                    <PopoverTrigger asChild>
                        <Button
                            variant="outline"
                            role="combobox"
                            aria-expanded={open}
                            className={cn(
                                'h-fit w-[200px] justify-between',
                                'overflow-hidden',
                                className,
                            )}
                        >
                            <div className="flex min-w-0 flex-wrap gap-1">
                                {selectedItems.length > 0
                                    ? selectedItems.map((v) =>
                                          allowMultiple ? (
                                              <Badge
                                                  variant="secondary"
                                                  key={v.value}
                                              >
                                                  {
                                                      getAllOptions().find(
                                                          (f) =>
                                                              f.value ===
                                                              v.value,
                                                      )?.label
                                                  }
                                              </Badge>
                                          ) : (
                                              <span
                                                  key={v.value}
                                                  className="truncate"
                                              >
                                                  {
                                                      getAllOptions().find(
                                                          (f) =>
                                                              f.value ===
                                                              v.value,
                                                      )?.label
                                                  }
                                              </span>
                                          ),
                                      )
                                    : 'Select ...'}
                            </div>
                            <ChevronsUpDown className="opacity-50" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent className="p-0">
                        <Command shouldFilter={false}>
                            <CommandInput
                                placeholder={`Search ...`}
                                className="h-9"
                                value={search}
                                onInput={(e) => {
                                    debouncedSearch(e.currentTarget.value);
                                }}
                            />
                            <CommandList>
                                {loading && (
                                    <CommandLoading>
                                        <div className="flex h-full items-center justify-center p-4">
                                            <Loader2 className="animate-spin" />
                                            <span>Fetching results...</span>
                                        </div>
                                    </CommandLoading>
                                )}
                                <CommandEmpty>No results found.</CommandEmpty>
                                <CommandGroup>
                                    {getAllOptions().map(
                                        (option: SelectOption) => (
                                            <CommandItem
                                                key={option.value}
                                                value={option.value}
                                                onSelect={(currentValue) => {
                                                    let newSelected: SelectOption[] =
                                                        [];

                                                    if (allowMultiple) {
                                                        // If selected item is already selected, remove it
                                                        if (
                                                            selectedItems
                                                                .map(
                                                                    (v) =>
                                                                        v.value,
                                                                )
                                                                .includes(
                                                                    currentValue,
                                                                )
                                                        ) {
                                                            newSelected =
                                                                selectedItems.filter(
                                                                    (v) =>
                                                                        v.value !==
                                                                        currentValue,
                                                                );
                                                        } else {
                                                            // If selected item is not already selected, add it to the full list
                                                            const newSelectedItem =
                                                                getAllOptions().find(
                                                                    (f) =>
                                                                        f.value ===
                                                                        currentValue,
                                                                );
                                                            newSelected = [
                                                                ...selectedItems,
                                                            ];

                                                            if (
                                                                newSelectedItem
                                                            ) {
                                                                newSelected.push(
                                                                    newSelectedItem,
                                                                );
                                                            }
                                                        }
                                                    } else {
                                                        // If selected item is already selected, remove it
                                                        if (
                                                            selectedItems
                                                                .map(
                                                                    (v) =>
                                                                        v.value,
                                                                )
                                                                .includes(
                                                                    option.value,
                                                                )
                                                        ) {
                                                            newSelected = [];
                                                        } else {
                                                            newSelected = [
                                                                option,
                                                            ];
                                                        }
                                                    }

                                                    if (
                                                        !allowUnselect &&
                                                        newSelected.length === 0
                                                    ) {
                                                        return;
                                                    }

                                                    // But save the whole selected for this component to reference
                                                    setSelectedItems(
                                                        newSelected,
                                                    );

                                                    // Just the ids for the on value change for parent users
                                                    valuesChangedHandler(
                                                        newSelected,
                                                    );
                                                }}
                                            >
                                                {option.label}
                                                <Check
                                                    className={cn(
                                                        'ml-auto',
                                                        selectedItems
                                                            .map((v) => v.value)
                                                            .includes(
                                                                option.value,
                                                            )
                                                            ? 'opacity-100'
                                                            : 'opacity-0',
                                                    )}
                                                />
                                            </CommandItem>
                                        ),
                                    )}
                                </CommandGroup>
                            </CommandList>
                        </Command>
                    </PopoverContent>
                </Popover>
                {createForm && (
                    <Button
                        type="button"
                        variant="ghost"
                        onClick={() => setNewFormOpen(true)}
                    >
                        <Plus />
                    </Button>
                )}
            </div>
            <Dialog open={newFormOpen} onOpenChange={setNewFormOpen}>
                <DialogContent>
                    <div className="flex flex-col gap-2">
                        {createForm &&
                            React.createElement(createForm, {
                                formRef: newFormRef,
                                onCreate: (data: CreateFormResult) => {
                                    const newSelectedId = data?.id?.toString();
                                    if (newSelectedId) {
                                        if (allowMultiple) {
                                            const newSelectedItems = [
                                                ...(selectedItems.map(
                                                    (v) => v.value,
                                                ) ?? []),
                                                newSelectedId,
                                            ];
                                            searchData('', newSelectedItems);
                                        } else {
                                            searchData('', [newSelectedId]);
                                        }
                                    }
                                    setNewFormOpen(false);
                                },
                            })}
                    </div>
                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            onClick={() => setNewFormOpen(false)}
                        >
                            Cancel
                        </Button>
                        <Button
                            type="button"
                            variant="default"
                            onClick={(e) => {
                                e.preventDefault();
                                e.stopPropagation();
                                newFormRef.current?.requestSubmit();
                            }}
                        >
                            Create
                        </Button>
                    </DialogFooter>
                </DialogContent>
            </Dialog>
        </>
    );
}
