{def $POIs = fetch( 'content', 'list', hash( 'parent_node_id', $block.custom_attributes.node_id,
                                                            'class_filter_type', 'include',
                                                            'class_filter_array', array( 'restaurant' ),
                                                            'sort_by', array( 'modified', false() ),
                                                            'limit', 10
                                                             ))
}
    
<!-- BLOCK: START -->
<div class="block-type-gallery">

<div class="border-box block-style6-box-outside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BLOCK BORDER INSIDE: START -->

<div class="border-box block-style1-box-inside">
<div class="border-tl"><div class="border-tr"><div class="border-tc"></div></div></div>
<div class="border-ml"><div class="border-mr"><div class="border-mc">
<div class="border-content">

<!-- BLOCK CONTENT: START -->

{include uri='design:gmap.tpl' 
         locations=$POIs
         size=array(440,600)
         show_popups_on_page=true()
         short_descriptive_attribute='short_description'
}

<!-- BLOCK CONTENT: END -->

</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

<!-- BLOCK BORDER INSIDE: END -->


</div>
</div></div></div>
<div class="border-bl"><div class="border-br"><div class="border-bc"></div></div></div>
</div>

</div>
<!-- BLOCK: END -->