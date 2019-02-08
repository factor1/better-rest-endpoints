# better-rest-endpoints
A WordPress plugin that serves up slimmer WP Rest API endpoints.

## Endpoints

### Posts
**`better-rest-endpoints/v1/posts`**
Gets a collection of posts. Accepts the following parameters:

- acf (boolean - setting to false omits `acf` from being returned)
- author (string) limit posts by author nice name (user_nicename)
- category id (int)
- category_name (string)
- content (boolean) set to false to omit content from showing in JSON response
- exclude (int) a post ID to exclude from the response
- media (boolean - setting to false omits `media` (featured media) from being returned)
- order (string - 'ASC' vs 'DESC')
- orderby (string)
- page (int)
- per_page (int)
- tag id  (int)
- yoast (boolean - setting to false omits `yoast` from being returned)

It returns a JSON response with the following:
- ACF fields, if applicable
- all possible thumbnail sizes & URL
- Author, user_nicename, & Author ID
- Categories
- Category IDs
- content
- date (ISO 8601)
- excerpt
- id
- slug
- Tag IDs
- Tags
- title
- Yoast SEO fields, if applicable

### Post
**`better-rest-endpoints/v1/post/{id}`**
Get a post by ID.

Accepts the following parameters:

- ID (int)

Returns a JSON response with the following:

- ACF fields, if applicable
- all possible thumbnail sizes & URLs
- Author, user_nicename, & Author ID
- Categories
- Category IDs
- content
- date (ISO 8601)
- excerpt
- id
- slug
- Tag IDs
- Tags
- title
- Yoast SEO fields, if applicable

### Post by slug
**`better-rest-endpoints/v1/post/{slug}`**
Get a post by ID.

Accepts the following parameters:

- slug (string)

Returns a JSON response with the following:

- ACF fields, if applicable
- all possible thumbnail sizes & URLs
- Author, user_nicename, & Author ID
- Categories
- Category IDs
- content
- date (ISO 8601)
- excerpt
- id
- slug
- Tag IDs
- Tags
- title
- Yoast SEO fields, if applicable

### Pages
**`better-rest-endpoints/v1/pages`**
Gets a collection of pages. Accepts the following parameters:

- acf (boolean - setting to false omits `acf` from being returned)
- content (boolean - setting to false hides the content from the response)
- exclude (int)
- exclude (int) a post ID to exclude from the response
- media (boolean - setting to false omits `media` (featured media) from being returned)
- order (string - 'ASC' vs 'DESC')
- orderby (string)
- page (int)
- per_page (int)
- yoast (boolean - setting to false omits `yoast` from being returned)

Returns the following JSON Response:

- ACF Fields
- all possible thumbnail sizes & URLs
- Content
- ID
- Slug
- Template Name
- Title
- Yoast SEO Fields

### Page by ID
**`better-rest-endpoints/v1/page/{id}`**
Get a page by ID.

Accepts the following parameters:

- ID (int)

Returns a JSON response with the following:

- ACF fields, if applicable
- all possible thumbnail sizes & URLs
- content
- id
- slug
- template name
- title
- Yoast SEO fields, if applicable

### Post by slug
**`better-rest-endpoints/v1/page/{slug|path}`**
Get a page by slug or path. Requesting a page by slug will only return a page with no parent with the requested slug. If multiple pages have the same slug the page needs to be requested by passing the entire path. eg. `better-rest-endpoints/v1/page/technology/about` or `better-rest-endpoints/v1/page/services/about` instead of just `better-rest-endpoints/v1/page/about`

Accepts the following parameters:

- slug, path (string)

Returns a JSON response with the following:

- ACF fields, if applicable
- all possible thumbnail sizes & URLs
- Author, user_nicename, & Author ID
- Categories
- Category IDs
- content
- date (ISO 8601)
- excerpt
- id
- slug
- Tag IDs
- Tags
- title
- Yoast SEO fields, if applicable

### Custom Post Type Collection
**`better-rest-endpoints/v1/{custom_post_type}`**
Gets a collection of posts from a custom post type. Accepts the following parameters:

- acf (boolean - setting to false omits `acf` from being returned)
- content (boolean - setting to false omits `the_content` from being returned)
- exclude (int) a post ID to exclude from the response
- media (boolean - setting to false omits `media` (featured media) from being returned)
- orderby (string) - see the [codex](https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters) for options, currently does not support multiple values
- page (int)
- per_page (int)
- yoast (boolean - setting to false omits `yoast` from being returned)

Returns the following JSON response:

- ACF fields if applicable
- all possible thumbnail sizes & URLs
- Author, user_nicename, & Author ID
- content
- date (ISO 8601)
- excerpt
- ID
- post terms
- slug
- title
- Yoast SEO fields if applicable

### Custom Post Type Post by ID
**`better-rest-endpoints/v1/{custom_post_type}/{id}`**
Gets a single custom post type item. Accepts the following parameters:

- ID

Returns the following JSON Response:

- ACF Fields, if applicable
- all possible thumbnail sizes & URLs
- Author, user_nicename, & Author ID
- content
- date (ISO 8601)
- excerpt
- ID
- post terms
- slug
- title
- Yoast SEO Fields, if applicable

### Custom Post Type Post by Slug
**`better-rest-endpoints/v1/{custom_post_type}/{slug}`**
Gets a single custom post type item. Accepts the following parameters:

- slug

Returns the following JSON Response:

- ACF Fields, if applicable
- all possible thumbnail sizes & URLs
- Author, user_nicename, & Author ID
- content
- date (ISO 8601)
- excerpt
- ID
- post terms
- slug
- title
- Yoast SEO Fields, if applicable

### Get Posts Belonging To A Taxonomy Term
**`better-rest-endpoints/v1/{taxonomy}/{term}`**
Gets posts from a taxonomy term. Accepts the following parameters:

- acf (boolean - setting to false omits `acf` from being returned)
- content (boolean - setting to false omits `the_content` from being returned)
- exclude (int) a post ID to exclude from the response
- media (boolean - setting to false omits `media` (featured media) from being returned)
- orderby (string) - see the [codex](https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters) for options, currently does not support multiple values
- page (int)
- per_page (int)
- yoast (boolean - setting to false omits `yoast` from being returned)

Returns the following JSON Response:

- ACF Fields, if applicable
- all possible thumbnail sizes & URLs
- Author, user_nicename, & Author ID
- content
- date (ISO 8601)
- excerpt
- ID
- post terms
- slug
- title
- Yoast SEO Fields, if applicable

### Menus from slug (name)
**`better-rest-endpoints/v1/menus/{menu-slug}`**
Gets a WordPress Menu by slug. Accepts no parameters.

Returns the following JSON Response in each item object:

- classes (array)
- description
- ID
- menu item parent
- menu_order
- slug
- target
- title
- url

### Menus from location (theme location)
**`better-rest-endpoints/v1/menus/{menu-slug}`**
Gets a WordPress Menu by the theme location. Accepts no parameters.

Returns an empty array if the location can not be found or if it has no assigned menu. Returns an array of the following objects if a menu is assigned to the specified location:

- classes (array)
- description
- ID
- menu item parent
- menu_order
- slug
- target
- title
- url

### Taxonomies
**`better-rest-endpoints/v1/taxonomies`**
Gets a list of taxonomies used by WordPress. Accepts no parameters.

Returns the following JSON response in each item object:

- Description
- Hierarchical (true/false)
- Name
- Slug

### Search
**`better-rest-endpoints/v1/search`**
Gets a collection of posts and pages based on the search parameter. Accepts the following parameters:

- acf (boolean - setting to false omits `acf` from being returned)
- category id (int)
- content (boolean) set to false to omit content from showing in JSON response
- media (boolean - setting to false omits `media` (featured media) from being returned)
- page (int)
- per_page (int)
- search (string | required)
- tag id  (int)
- yoast (boolean - setting to false omits `yoast` from being returned)

It returns a JSON response with the following (returns an empty array if no posts found):
- ACF fields, if applicable
- all possible thumbnail sizes & URL
- Author, user_nicename, & Author ID
- Categories
- Category IDs
- content
- date (ISO 8601)
- excerpt
- id
- slug
- Tag IDs
- Tags
- title
- Yoast SEO fields, if applicable

### ACF Options
**`better-rest-endpoints/v1/options/acf`**
Gets an array of all ACF Options Page fields, returns an empty array if none are found or if ACF is not active.

**`better-rest-endpoints/v1/options/acf/{field}`**
Gets a single ACF Options Page field, returns null if ACF is not active or the field does not exist.

Accepts the following parameters:

- field (string - can be either the field key or the field name)

## Hooks and Filters

### Filter the Custom Post Types endpoints

```php
add_filter('better_rest_endpoints_cpt_collection', function($cpt_collection){
	$cpt_collection = array_flip($cpt_collection);
	unset($cpt_collection['oembed_cache']);
	unset($cpt_collection['_pods_template']);
	unset($cpt_collection['_pods_pod']);
	unset($cpt_collection['_pods_field']);
	$cpt_collection = array_values( array_flip($cpt_collection) );
	return $cpt_collection;
});
```
