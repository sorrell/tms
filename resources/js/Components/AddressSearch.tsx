import GooglePlacesAutocomplete from 'react-google-places-autocomplete';
import { geocodeByAddress } from 'react-google-places-autocomplete';
import { Location } from '@/types';

export default function AddressSearch({onAddressSelect}: {onAddressSelect: (location: Location) => void}) {

    const findFullDetails = (address: string) => {
        geocodeByAddress(address)
            .then(results => {
                if (results && results.length > 0) {
                    const result = results[0];
                    const addressComponents = result.address_components;
                    
                    let street_number = '';
                    let route = '';
                    let city = '';
                    let state = '';
                    let zipcode = '';
                    
                    // Extract address components
                    addressComponents.forEach(component => {
                        const types = component.types;
                        
                        if (types.includes('street_number')) {
                            street_number = component.long_name;
                        }
                        
                        if (types.includes('route')) {
                            route = component.long_name;
                        }
                        
                        if (types.includes('locality')) {
                            city = component.long_name;
                        }
                        
                        if (types.includes('administrative_area_level_1')) {
                            state = component.long_name;
                        }
                        
                        if (types.includes('postal_code')) {
                            zipcode = component.long_name;
                        }
                    });
                    
                    // Create Location object
                    const location: Location = {
                        id: -1,
                        name: result.formatted_address,
                        address_line_1: street_number ? `${street_number} ${route}` : route,
                        address_line_2: '',
                        address_city: city,
                        address_state: state,
                        address_zipcode: zipcode,
                        selectable_label: result.formatted_address
                    };
                    
                    // Call the onAddressSelect prop with the location
                    onAddressSelect(location);
                }
            })
            .catch(error => console.error(error));
    }
    
    return (<GooglePlacesAutocomplete
      apiKey={import.meta.env.VITE_GOOGLE_MAPS_API_KEY}
      selectProps={{onChange: (result: any) => { findFullDetails(result?.label); }}}
    />);
}