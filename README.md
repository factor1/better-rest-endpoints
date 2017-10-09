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
- Author & Author ID
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
- Author & Author ID
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
- Template Name
- Title
- Content
- ACF Fields
- all possible thumbnail sizes & URLs

### Page by ID
**`better-wp-endpoints/v1/page/{id}`**
Get a page by ID.

Accepts the following parameters:

- ID (int)

Returns a JSON response with the following:

- id
- slug
- title
- template name
- content
- all possible thumbnail sizes & URLs
- ACF fields, if applicable

### Custom Post Type Collection
**`better-wp-endpoints/v1/{custom_post_type}`**
Gets a collection of posts from a custom post type. Accepts the following parameters:

- per_page (int)
- page (int)
- content (boolean - setting to false omits `the_content` from being returned)

Returns the following JSON response:

- ID
- slug
- title
- date (ISO 8601)
- post terms
- excerpt
- content
- all possible thumbnail sizes & URLs
- Author & Author ID
- ACF fields if applicable

### Custom Post Type Post
**`better-wp-endpoints/v1/{custom_post_type}/{id}`**
Gets a single custom post type item. Accepts the following parameters:

- ID

Returns the following JSON Response:

- ID
- slug
- title
- date (ISO 8601)
- post terms
- excerpt
- content
- all possible thumbnail sizes & URLs
- Author & Author ID
- ACF Fields, if applicable

### Menus
**`better-wp-endpoints/v1/menus/{menu-slug}`**
Gets a WordPress Menu by slug. Accepts no parameters.

Returns the following JSON Response in each item object:

- ID
- menu_order
- title
- url
- slug 
- target
- description
- classes (array)
- menu item parent
