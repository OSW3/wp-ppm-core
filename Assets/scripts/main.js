(function($){$(document).ready(function(){
    

    // ****************************** //
    // Collection
    // ****************************** //

    // Create Collections
    var Collections = new Object();

    // Initialize Collections
    $('[data-ppm-collection]').each(function() {
        
        var name = $(this).data('ppmCollection');
        
        Collections[name] = {

            // The name of the Collection
            name: name,

            // Collection Wrapper element
            wrapper: $(this),

            // Items container
            container: $(this).find('[data-role=container]'),

            // Item prototype
            prototype: $(this).find('[data-role=prototype]'),

            // Button Add item
            add: $(this).find('[data-role=control][data-control=add]'),

            items: undefined,

            min: $(this).data('init') || 1,

            // Collection Stats
            stats: {
                serial: 0, 
                items: 0
            }

        };
    });

    // Initialize each collection
    $.each(Collections, function(key, collection) {
        
        var containerHTML = collection.container.html();
            containerHTML = containerHTML.replace(/{{serial}}/g, '<span data-role="serial"></span>');
            collection.container.html(containerHTML);

        // Collection Items (already added to the container)
        var items = collection.container.find('[data-role=item]');

        Collections[collection.name].items = items;
        Collections[collection.name].stats.serial = items.length;
        Collections[collection.name].stats.items = items.length;

        // Refresh Items Serial
        ppmCollection_refreshSerial(collection);

        // Add an Item
        collection.add.click(function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            ppmCollection_addItem(collection);
        });
        
    });

    // Add new item to collection
    function ppmCollection_addItem(collection) {

        // Clone the prototype
        var clone = collection.prototype.clone();

        // Extract HTML tag of an item from the clone
        var item = clone.html();
            item = item.replace(/{{number}}/g, Collections[collection.name].stats.serial);
            item = item.replace(/{{serial}}/g, '<span data-role="serial"></span>');

        // Add item to container
        collection.container.append(item);

        // Update Serial
        Collections[collection.name].stats.serial++;

        // Refresh Items Serial
        ppmCollection_refreshSerial(collection);
    }

    // Remove an item from collection
    function ppmCollection_removeItem(collection, item) {

        item.remove();
        ppmCollection_refreshSerial(collection);
    }

    // Refresh each items serial
    function ppmCollection_refreshSerial( collection ) {

        var items = collection.container.find('[data-role=item]');

        collection.container.find('[data-role=alert]').toggleClass('hidden', (items.length > 0));

        $.each(items, function(key, item){

            // Set the serial number
            $(item).find('[data-role=serial]').text(key+1);

            // Find Control Element
            var controlTag = $(item).find('[data-role=control][data-control=remove]');

            // Show / Hide remove button
            items.length > collection.min
                ? controlTag.removeClass('hidden')
                : controlTag.addClass('hidden');

            // Remove item
            controlTag.click(function(e){
                e.preventDefault();
                e.stopImmediatePropagation();
                ppmCollection_removeItem(collection, item);
            });
        });
    }


    // ****************************** //
    // Output field
    // ****************************** //

    $('output').each(function(){

        var attrFor = $(this).attr('for');

        if (attrFor != undefined)
        {
            var inputs = attrFor.split(' ');
            var output = $(this);
    
            outputCalculation(inputs, output);
            
            $.each(inputs, function(index, element) {
                $('#'+element).on('change', function() {
                    outputCalculation(inputs, output);
                });
            });
        }
    });

    function outputCalculation(inputs, output) 
    {
        var sum = 0;

        $.each(inputs, function(index, element) {
            var val = parseInt($('#'+element).val());
                val = isNaN(val) ? 0 : val;

            sum += val;
        });
        output.val(sum);
    }
    

    // ****************************** //
    // Textaera autosize
    // ****************************** //

    autosize($('textarea.ppm-control.autosize'));

    
});}(jQuery));