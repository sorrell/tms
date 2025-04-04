import { Shipment } from '@/types';
import { useEffect, useState } from 'react';
import { useLoadScript, GoogleMap, Marker } from '@react-google-maps/api';

// Set default map container style
const mapContainerStyle = {
  width: '100%',
  height: '400px',
};

// Default center (will be overridden by actual data)
const defaultCenter = {
  lat: 39.8283, // Default US center
  lng: -98.5795,
};

export default function LocationMap({ shipment }: { shipment: Shipment; }) {
  const [mapCenter, setMapCenter] = useState(defaultCenter);
  const [markers, setMarkers] = useState<Array<{
    lat: number;
    lng: number;
    label: string;
    stopType: string;
  }>>([]);

  // Load Google Maps script
  const { isLoaded, loadError } = useLoadScript({
    googleMapsApiKey: import.meta.env.VITE_GOOGLE_MAPS_API_KEY || '',
  });

  useEffect(() => {
    if (shipment && shipment.stops && shipment.stops.length > 0) {
      // Process stops with valid facilities and locations
      const validStops = shipment.stops.filter(
        stop => stop.facility && stop.facility.location
      );

      if (validStops.length > 0) {
        // Process addresses into geocoded points
        const geocodeAddresses = async () => {
          const newMarkers = await Promise.all(
            validStops.map(async (stop, index) => {
              const location = stop.facility?.location;
              if (!location) return null;

              // Form the full address for geocoding
              const address = `${location.address_line_1}, ${location.address_city}, ${location.address_state} ${location.address_zipcode}`;
              
              try {
                // For a real implementation, you would use a geocoding service here
                // This is a simplified example that would need actual geocoding
                // For now, we'll simulate it with some calculation based on index
                // In production, use the Google Geocoding API or a similar service
                
                // Simulated coordinates for demonstration
                const lat = defaultCenter.lat + (index * 0.5) * (Math.random() > 0.5 ? 1 : -1);
                const lng = defaultCenter.lng + (index * 0.5) * (Math.random() > 0.5 ? 1 : -1);
                
                return {
                  lat,
                  lng,
                  label: `${index + 1}`,
                  stopType: stop.stop_type,
                };
              } catch (error) {
                console.error('Geocoding error:', error);
                return null;
              }
            })
          );

          // Filter out null values
          const filteredMarkers = newMarkers.filter(marker => marker !== null) as Array<{
            lat: number;
            lng: number;
            label: string;
            stopType: string;
          }>;

          // Update markers
          setMarkers(filteredMarkers);

          // Set center to first marker if available
          if (filteredMarkers.length > 0) {
            setMapCenter({
              lat: filteredMarkers[0].lat,
              lng: filteredMarkers[0].lng,
            });
          }
        };

        geocodeAddresses();
      }
    }
  }, [shipment]);

  if (loadError) {
    return <div>Error loading maps</div>;
  }

  if (!isLoaded) {
    return <div>Loading maps...</div>;
  }

  return (
    <div>
      <GoogleMap
        mapContainerStyle={mapContainerStyle}
        zoom={5}
        center={mapCenter}
      >
        {markers.map((marker, index) => (
          <Marker
            key={index}
            position={{ lat: marker.lat, lng: marker.lng }}
            label={{ text: marker.label, color: "white" }}
          />
        ))}
      </GoogleMap>

      <div className="mt-4">
        <div className="flex items-center space-x-6 text-sm">
          <div className="flex items-center">
            <div className="w-4 h-4 rounded-full bg-red-500 mr-2"></div>
            <span>Pickup Locations</span>
          </div>
          <div className="flex items-center">
            <div className="w-4 h-4 rounded-full bg-blue-500 mr-2"></div>
            <span>Delivery Locations</span>
          </div>
        </div>
        <p className="mt-2 text-sm text-gray-500">
          Note: For this map to work, you need to add a Google Maps API key to your environment variables.
        </p>
      </div>
    </div>
  );
}