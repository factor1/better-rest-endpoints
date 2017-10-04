# better-wp-endpoints
A WordPress plugin that serves up slimmer WP Rest API endpoints - WIP

## Endpoints

### Posts
**`better-wp-endpoints/v1/posts`**
Gets a collection of posts. Accepts the following parameters:

- page (int)
- per_page (int)
- category id (int)
- tag id  (int)
- content (boolean) set to false to omit content from showing in JSON response

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
- ACF fields, if applicable

### Post
**`better-wp-endpoints/v1/post/{id}`**
Get a post by ID.

Accepts the following parameters:

- ID (int)

Returns a JSON response with the following:

- id
- slug
- title
- date (ISO 8601)
- excerpt
- content
- all possible thumbnail sizes & URLs
- Author & Author Link
- Categories
- Category IDs
- Tags
- Tag IDs
- ACF fields, if applicable

### Pages
**`better-wp-endpoints/v1/pages`**
Gets a collection of pages. Accepts the following parameters:

- exclude (int)
- orderby (string)
- order (string - 'ASC' vs 'DESC')
- per_page (int)
- page (int)
- content (boolean - setting to false hides the content from the response)

Returns the following JSON Response:

- ID
- Slug
- Title
- Content
- ACF Fields
- all possible thumbnail sizes & URLs
