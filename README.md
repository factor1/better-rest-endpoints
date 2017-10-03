# better-wp-endpoints
A WordPress plugin that serves up slimmer WP Rest API endpoints - WIP

## Endpoints
**`better-wp-endpoints/v1/posts`**
Gets a collection of posts. Accepts the following arguments:

- page (int)
- per_page (int)
- category id
- tag id 

It returns a JSON response with the following:
- id
- slug
- title
- date (ISO 8601)
- excerpt
- content
- all possible thumbnail sizes & URL
- Author & Author Link
- Categories
- Category IDs
- Tags
- Tag IDs
- Custom Taxonomies
- ACF fields
