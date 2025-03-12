// resources/js/utils/timezone.ts
/**
 * Converts IANA timezone format (e.g., "America/New_York") to shorthand format (e.g., "EST")
 */
export function getTimezoneShorthand(timeZone: string): string {
    try {
        return (
            new Intl.DateTimeFormat('en', {
                timeZone: timeZone,
                timeZoneName: 'short',
            })
                .formatToParts(new Date())
                .find((part) => part.type === 'timeZoneName')?.value || timeZone
        );
    } catch (error) {
        console.error('Error converting timezone:', error);
        return timeZone;
    }
}

/**
 * Gets the current user's browser timezone in IANA format (e.g., "America/New_York")
 */
export function getUserBrowserTimezone(): string {
    return Intl.DateTimeFormat().resolvedOptions().timeZone;
}
