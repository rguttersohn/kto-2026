# Keeping Track Online Database 2026

This repo is the codebase for Keeping Track Online 2026.

## Stack Overview

This codebase is built on the Laravel 12.x framework and largely tries to follow the patterns recommended by the framework.

It runs on two databases:

1. An internal Postgres database (called **Internal DB** from here on out).
2. An external Postgres database that runs on the Supabase platform (called **Supabase DB** from here on out).

### Internal DB

The Internal DB largely handles user data and CMS matters.

### Supabase DB

The Supabase DB holds the statistical data on New York's children and the location data. It is broken up into the following schemas:

1. Locations: Data on locations, location types, and geometries
2. Indicators: Information about indicators, data, and filters
3. Assets: Asset categories and their assets with their location data
4. Collections: Data on collections and their respective data tables

## 🔍 Filters and Sorting

This project includes two reusable Eloquent traits for safely applying filters and sorts to queries using whitelists and optional aliasing:

- `App\Models\Traits\Filterable`
- `App\Models\Traits\Sortable`

These traits are designed for use in API endpoints or internal logic where dynamic query input needs to be parsed and constrained securely.

---

### Filterable

Use the `Filterable` trait to apply dynamic filters (including JSONB fields) via a consistent and safe syntax.

#### Supported Operators

| Operator  | SQL Equivalent | Notes                      |
|-----------|----------------|----------------------------|
| `eq`      | =              |                            |
| `neq`     | !=             |                            |
| `gt`      | >              |                            |
| `gte`     | >=             |                            |
| `lt`      | <              |                            |
| `lte`     | <=             |                            |
| `in`      | IN             | Accepts arrays             |
| `nin`     | NOT IN         | Accepts arrays             |
| `null`    | IS NULL        |                            |
| `notnull` | IS NOT NULL    |                            |

#### Configuration (per model)

```php
use App\Models\Traits\Filterable;

class Post extends Model
{
    use Filterable;

    protected array $filter_whitelist = ['title', 'status', 'metadata'];

    protected array $filter_aliases = [
        'state' => 'status',
    ];

    protected array $jsonb_columns = ['data'];
}
```

#### Example usage

```php
$filters = [
    'state' => ['eq' => 'published'],
    'data.author' => ['eq' => 'Jane Doe'],
    'data.tags' => ['in' => ['news', 'policy']],
];

$posts = Post::query()
    ->filter($filters)
    ->get();
```

#### Sortable

Use the `Sortable` trait to safely apply ORDER BY clauses using a whitelist and optional aliases.

#### Configuration (Per Model)

```php

use App\Models\Traits\Sortable;

class Post extends Model
{
    use Sortable;

    protected array $sort_whitelist = ['title', 'created_at'];

    protected array $sort_aliases = [
        'date' => 'created_at',
    ];
}


```

#### Example Usage

```php

$sorts = [
    'date' => 'desc',
    'title' => 'asc',
];

$posts = Post::query()
    ->applySorts($sorts)
    ->get();

```

### Query Examples

To make use of the filter and sort traits in a RESTful API, structure your query params like so:

#### Filter Examples:

```
GET /api/posts?filter[state][eq]=published
GET /api/posts?filter[data.author][eq]=Jane%20Doe
GET /api/posts?filter[data.tags][in][]=news&filter[data.tags][in][]=policy
```

#### Sort Examples: 

```
GET /api/posts?sort[date]=desc
GET /api/posts?sort[title]=asc
```
#### Combined Examples: 

```
GET /api/posts?filters[state][eq]=published&sorts[date]=desc

```