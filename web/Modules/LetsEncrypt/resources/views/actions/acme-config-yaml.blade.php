contact_email: {{ $email }}

defaults:
  distinguished_name:
    country: {{ $country }}
    locality: {{ $locality }}
    organization_name: {{ $organization }}
  solver: http

certificates:
  - domain: {{ $domain }}
    solver:
      subject_alternative_names: www.{{ $domain }}
      name: http-file
      adapter: local
      root: {{ $domainPublic }}
    @if(isset($wildcard) && $wildcard)

    @endif
