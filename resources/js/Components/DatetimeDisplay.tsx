import { HTMLAttributes } from 'react';

export default function DatetimeDisplay({
    datetime,
    className = '',
    ...props
}: HTMLAttributes<HTMLDivElement> & { datetime?: string }) {

    let formattedDatetime = datetime;

    if (datetime) {
        formattedDatetime = new Date(datetime).toLocaleString();
    }

    return (
        <div
            {...props}
            className={className}
        >
            {formattedDatetime}
        </div>
    )
}
