contact_email: {{ $email }}

defaults:
  distinguished_name:
    country: {{ $country }}
    locality: {{ $locality }}
    organization_name: {{ $organization }}
  solver: dns

certificates:
  - domain: '*.{{ $domain }}'
    solver: dns
    subject_alternative_names:
    - {{ $domain }}
