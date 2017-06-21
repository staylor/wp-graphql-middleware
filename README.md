# wp-graphql-middleware
Extend the WordPress API with data to build headless themes with Relay and GraphQL.

## Installation

This code is still in active development. Clone this repo in your `wp-content/plugins` directory, then activate the plugin in your WordPress admin. `git pull` to receive updates.

## REST API Extensions

This plugin extends the existing WordPress REST API to provide more data than is currently available. However, instead of filtering the current endpoints, news endpoint are provided that extend the existing endpoints. This ensures that the core endpoints work exactly as you expect.

Most of these endpoints remove the `_links` property, which is a JSON Schema construct not embraced by GraphQL.

### `graphql/v1/comments`

This endpoint exists primarily to aid in creating a robust commenting UI for your app. The Comments endpoints in the REST API require authentication for most CRUD operations, but this plugin adds a filter to allow anonymous comment creation via the REST API. 

`prepare_item_for_response()` has been extended to:
* add `Set-Cookie` headers to the response, so you can identify your users on the front end. 
* provide the `raw` value alongside the `rendered` value for comments to allow quick access for editing - TODO: actual edits and authentication do not work yet.
* provide a `author_hash` value for each comment, to determine if the current user "owns" any of the anonymous comments - TODO: still determining an "auth" flow for safely allowing an "anonymous" user to edit or delete their comments.

### `graphql/v1/nav-menus`

The current set of endpoints do not expose Nav Menus, which is problematic if you are building an entire headless theme (a theme that does not actually live on top of WordPress proper). This endpoint does not allow Nav Menus to be edited.

There are 2 readonly routes:
* `graphql/v1/menus`, which returns the set of menus (and their items) returned by `wp_get_nav_menus()`
* `graphql/v1/menus/<menu>`, which hydrates the menu returned by `wp_get_nav_menu_object( $request['menu'] )`

TODO: support pagination args, so that people who build their site with a bunch of menus don't hit a wall after page 1 of results.

### `graphql/v1/posts`

This endpoint exists so that `raw` values are always returned alongside `rendered` values for `content` and `excerpt`. This is important so you don't have to parse HTML strings on the front end when setting values for `<meta>` tags, etc. Scripts and JSON are also properly stripped.

### `graphql/v1/types`

Returns a `labels` property for each type, containing 2 nodes: `singular` and `plural`, so display names can be used where appropriate.

### `graphql/v1/settings`

When creating "themes" or "apps" that use a CMS backend, it is best if most strings are dynamic, and don't require a code deploy to change them on a live site. The Settings endpoint provides some of this data, but it is not readable without auth when using the core endpoint. This endpoint simply allows Settings to be read.

### `graphql/v1/sidebars`

The current set of endpoints do not expose Sidebars/Widgets, which is problematic if you are building an entire headless theme (a theme that does not actually live on top of WordPress proper). This endpoint does not allow Sidebars/Widgets to be edited.

There are 2 readonly routes:
* `graphql/v1/sidebars`, which returns the sidebars (and their widgets) available by the PHP Global™ `$wp_registered_sidebars`
* `graphql/v1/sidebars/<sidebar>`, which hydrates the sidebar (and its widgets) returned by `$wp_registered_sidebars[ $request['sidebar'] ]`

### `graphql/v1/taxonomies`

Returns a `labels` property for each type, containing 2 nodes: `singular` and `plural`, so display names can be used where appropriate.
Returns a property, `rewrite`, that contains 1 node: `slug`, which is a hint that can be used on the frontend to construct routes. This helps avoid having to write a translation layer for Taxonomy Name -> Pretty Name. Think: `post_tag` and `tag`. This is easiest way to know what is happening. By doing this, I was able to create a `Term` route, that supports all taxonomies. Not a requirement, but easier to do when you know what the URLs might look like when building.
