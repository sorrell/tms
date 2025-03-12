import { HTMLAttributes } from 'react';

export default function DatetimeDisplay({
    datetime,
    timezone,
    className = '',
    ...props
}: HTMLAttributes<HTMLDivElement> & { datetime?: string, timezone?: string }) {
    let formattedDatetime = datetime;

    if (formattedDatetime) {

        if (formattedDatetime.substring(formattedDatetime.length - 1) !== 'Z') {
            formattedDatetime = formattedDatetime + 'Z';
        }

        formattedDatetime = new Date(formattedDatetime).toLocaleString('en-US', { timeZone: timezone });
    }

    return (
        <div {...props} className={className}>
            {formattedDatetime !== '' ? formattedDatetime : 'Not set'}
        </div>
    );
}
