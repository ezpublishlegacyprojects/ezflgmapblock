{if is_set( $block.custom_attributes.node_id )}
	{def $POI = fetch( 'content', 'list', hash( 'parent_node_id', $block.custom_attributes.node_id,
																'class_filter_type', 'include',
																'class_filter_array', array( 'restaurant' ),
																'sort_by', array( 'modified', false() ),
																'offset', 0,
																'limit', 1
																 )).0}
<div class="block-type-tagcloud block-view-{$block.view}">

<div class="attribute-header"><h2><a href={$POI.url_alias|ezurl()}>{$POI.name}</a></h2></div>

<div class="attribute-long">{attribute_view_gui attribute=$POI.data_map.location type='tiny'}</div>

</div>
{/if}