// Sync desktop & mobile view checkboxes
function SyncCheckboxes(product_id, is_from_desktop = true) {
    // Get elements of desktop & mobile checkboxes
    var checkbox_desktop    = $('#desktop_'+product_id)[0];
    var checkbox_mobile     = $('#mobile_'+product_id)[0];

    // Sync checked status of checkboxes depending on origin
    if (is_from_desktop) {
        checkbox_mobile.checked = checkbox_desktop.checked;
    } else {
        checkbox_desktop.checked = checkbox_mobile.checked;
    }

    if (checkbox_mobile.checked) {
        AddToItemList(product_id);
    } else {
        RemoveFromItemList(product_id);
    }
    ToggleItemList();
}

// Add/Remove items from item list
function AddToItemList(product_id) {
    var itemList_container = $('#item_list_container');

    $('<input>').attr({
        id: "item_"+product_id,
        name: "item[]",
        type: "hidden", 
        value: product_id
    }).appendTo(itemList_container);
}
function RemoveFromItemList(product_id) {
    var itemList_container = $('#item_list_container');
    
    itemList_container.children('#item_'+product_id)[0].remove();
}

function ToggleItemList() {
    var itemList_container  = $('#item_list_container');
    var item_list_wrapper   = $('.item_list_wrapper')[0];

    if (itemList_container.children("input").length == 0) {
        item_list_wrapper.style.display = "none";
    } else {
        item_list_wrapper.style.display = "block";
    }
}

// Toggle display of table contents
function toggle_display(div_id) {
    var x = $('#'+div_id)[0];
    if (x.style.display === "none") {
        x.style.display = "table-cell";
    } else {
        x.style.display = "none";
    }
} 

// Admin-use: Set the brand name in the edit field 
function set_edit_brand_name(select, target_div_id) {
    var selected = select.options[select.selectedIndex];
    var target_div = $('#'+target_div_id)[0];
    var target_input = $('#'+target_div_id).children('#name')[0];

    if (selected.value == "") {
        target_div.style.display = "none";
    } else {
        target_div.style.display = "block";
        target_input.value = selected.innerHTML;
    }
}

var product_categories = ['product_title', 'product_brand', 'product_type', 'release_date', 'price_jp', 'price_hk', 'price_diff'];
function sortTable_v2(category_id, is_number, is_asc) {
    var flag_sorting      = true;       // Continue performing sorting
    var flag_exec_swap    = false;      // Determine whether to execute swap between current and next row
    var entry_items;                    // Variable for getting current state of list

    while (flag_sorting) {
        flag_sorting = false;

        entry_items = document.getElementsByClassName('entry_item');    // Update entry items to current set

        var object_row_current, object_row_next, value_current, value_next; 
        for (i = 0; i < (entry_items.length - 1); i++) {
            flag_exec_swap = false;

            object_row_current  = entry_items[i].getElementsByClassName('value_container')[0].getElementsByClassName(product_categories[category_id])[0].innerHTML;
            object_row_next     = entry_items[i+1].getElementsByClassName('value_container')[0].getElementsByClassName(product_categories[category_id])[0].innerHTML;

            if (!is_number) {
                value_current   = object_row_current.toLowerCase();
                value_next      = object_row_next.toLowerCase();
            } else {
                value_current   = Number(object_row_current);
                value_next      = Number(object_row_next);
            }

            // Test-use (check if variables read properly)
            //alert(value_current + " | " + value_next);

            if (is_asc) {
                if (value_current > value_next) {
                    flag_exec_swap = true;
                    break;
                }
            } else {
                if (value_current < value_next) {
                    flag_exec_swap = true;
                    break;
                }
            }
        }
        if (flag_exec_swap) {
            entry_items[i].parentNode.insertBefore(entry_items[i+1], entry_items[i]);
            flag_sorting = true;
        }
    }
}

function ExecuteTableSort() {
    var dropmenu_sort = $('#select_sort')[0].selectedIndex;
    switch (dropmenu_sort) {
        // Release Date (Default)
        case 0:     sortTable_v2(3, false, false);  break;
        case 1:     sortTable_v2(3, false, true);   break;

        // Product Name
        case 2:     sortTable_v2(0, false, true);   break;
        case 3:     sortTable_v2(0, false, false);  break;
        // Brand
        case 4:     sortTable_v2(1, false, true);   break;
        case 5:     sortTable_v2(1, false, false);  break;
        // Type
        case 6:     sortTable_v2(2, false, true);   break;
        case 7:     sortTable_v2(2, false, false);  break;
        // JP Price (JPY)
        case 8:     sortTable_v2(4, true, true);    break;
        case 9:    sortTable_v2(4, true, false);   break;
        // HK Price (HKD)
        case 10:    sortTable_v2(5, true, true);    break;
        case 11:    sortTable_v2(5, true, false);   break;
        // Price Diff. (JP - HK)
        case 12:    sortTable_v2(6, true, true);    break;
        case 13:    sortTable_v2(6, true, false);   break;
    }
}