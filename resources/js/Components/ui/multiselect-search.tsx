'use client';

import { Check, ChevronsUpDown, Loader2 } from 'lucide-react';
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
import axios from 'axios';
import { CommandLoading } from 'cmdk';

export function MultiSelectSearch() {
    const [open, setOpen] = React.useState(false);
    const [selectedItems, setSelectedItems] = React.useState<any[]>([]);
    const [search, setSearch] = React.useState('');
    const [dataOptions, setDataOptions] = React.useState<any[]>([]);
    const [loading, setLoading] = React.useState(false);
    const searchTimeout = React.useRef<NodeJS.Timeout>();

    const searchData = (searchInput: string) => {
        setLoading(true);
        axios
            .get(route('facilities.search'), {
                params: {
                    query: searchInput,
                },
            })
            .then((response) => {
                let options = response.data.map((item: any) => ({
                    value: item.id.toString(),
                    label: item.name,
                }));
                setSearch(searchInput);
                setDataOptions(options);
                setLoading(false);
            })
            .catch(() => {
                setLoading(false);
            });
    };

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
                // Sorting by selected items first then alphabetic second
                const aSelected = selectedItems.find(
                    (s) => s.value === a.value,
                );
                const bSelected = selectedItems.find(
                    (s) => s.value === b.value,
                );

                if (aSelected && !bSelected) return -1;
                if (!aSelected && bSelected) return 1;

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
        <Popover open={open} onOpenChange={setOpen}>
            <PopoverTrigger asChild>
                <Button
                    variant="outline"
                    role="combobox"
                    aria-expanded={open}
                    className="w-[200px] justify-between"
                >
                    <div className="overflow-hidden">
                        {selectedItems.length > 0
                            ? selectedItems
                                  .map(
                                      (v) =>
                                          getAllOptions().find(
                                              (f) => f.value === v.value,
                                          )?.label,
                                  )
                                  .join(', ')
                            : 'Select ...'}
                    </div>
                    <ChevronsUpDown className="opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent className="p-0">
                <Command shouldFilter={false}>
                    <CommandInput
                        placeholder="Search..."
                        className="h-9"
                        value={search}
                        onInput={(e) => {
                            debouncedSearch(e.currentTarget.value);
                        }}
                    />
                    <CommandList>
                        {loading && (
                            <CommandLoading>
                                <div className="flex items-center justify-center h-full p-4">
                                    <Loader2 className="animate-spin" />
                                    <span>Fetching results...</span>
                                </div>
                            </CommandLoading>
                        )}
                        <CommandEmpty>No results found.</CommandEmpty>
                        <CommandGroup>
                            {getAllOptions().map((option: any) => (
                                <CommandItem
                                    key={option.value}
                                    value={option.value}
                                    onSelect={(currentValue) => {
                                        if (
                                            selectedItems
                                                .map((v) => v.value)
                                                .includes(currentValue)
                                        ) {
                                            setSelectedItems(
                                                selectedItems.filter(
                                                    (v) =>
                                                        v.value !==
                                                        currentValue,
                                                ),
                                            );
                                        } else {
                                            setSelectedItems([
                                                ...selectedItems,
                                                getAllOptions().find(
                                                    (f) =>
                                                        f.value ===
                                                        currentValue,
                                                ),
                                            ]);
                                        }
                                    }}
                                >
                                    {option.label}
                                    <Check
                                        className={cn(
                                            'ml-auto',
                                            selectedItems
                                                .map((v) => v.value)
                                                .includes(option.value)
                                                ? 'opacity-100'
                                                : 'opacity-0',
                                        )}
                                    />
                                </CommandItem>
                            ))}
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    );
}
