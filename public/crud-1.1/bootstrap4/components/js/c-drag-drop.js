crud.components.cDragDrop = Vue.component('c-drag-drop', {
    extends: crud.components.views.vList,
    template: '#c-drag-drop-template',
    methods : {
        onDragStart : function(event) {
            event
            .dataTransfer
            .setData('text/plain', jQuery(event.target).attr('data-item-id'));
            console.log(event.target);
        },
        onDragOver : function(event) {
            event.preventDefault();
        },
        onDrop : function(event) {
            var that = this;
            const id = event
                .dataTransfer
                .getData('text');
            console.log('id',id,event);
            EVENT = event;
            //const draggableElement = document.getElementById(id);
            const draggableElement = that.jQe('[data-item-id="'+id+'"]');
            jQuery(event.target).closest('[data-bucket-id]').append(draggableElement);
        },
        getBuckets : function () {
            var that = this;
            var buckets = [];
            jQuery.each(that.jQe('[data-bucket-id]'),function () {
                var items = [];
                jQuery.each(jQuery(this).find('[data-item-id]'),function () {
                    items.push(jQuery(this).attr('data-item-id'));
                })
                buckets.push({
                    id: jQuery(this).attr('data-bucket-id'),
                    items : items
                })
            })
            return buckets;
        }
    }
})
