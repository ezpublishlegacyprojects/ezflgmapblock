{if is_set( $block.custom_attributes.node_id )}
	{def $geolocated_article = fetch( 'content', 'list', hash( 'parent_node_id', $block.custom_attributes.node_id,
																'class_filter_type', 'include',
																'class_filter_array', array( 'article' ),
																'sort_by', array( 'modified', false() ),
																'offset', 0,
																'limit', 1
																 )).0}
<div class="block-type-tagcloud block-view-{$block.view}">

<div class="attribute-header"><h2><a href={$geolocated_article.url_alias|ezurl()}>{$geolocated_article.name}</a></h2></div>

<div class="attribute-long">{attribute_view_gui attribute=$geolocated_article.data_map.location type='streetview'}</div>

</div>
{/if}