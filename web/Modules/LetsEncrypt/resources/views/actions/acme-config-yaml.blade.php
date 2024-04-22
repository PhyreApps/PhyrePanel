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
      name: http-file
      adapter: local
      root: {{ $domainPublic }}
    @if(isset($wildcard) && $wildcard)

    @endif
