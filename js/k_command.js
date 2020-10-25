if (window.addEventListener) {
    var keys = [], 
        kCommand = "38,38,40,40,37,39,37,39,66,65";

    window.addEventListener("keydown", function(e){
        keys.push(e.keyCode);

        // Run once key inputs fully match the kCommand array entries
        if (keys.toString().indexOf(kCommand) >= 0) {
            // Get all classes with default class
            var target_classes_header  = $('.container_header');
            var target_classes_content = $('.container_content');

            // alert(target_classes_header[1].innerHTML);

            for (i = 0; i < target_classes_header.length; i++) {
                target_classes_header[i].classList.add('container_header_kde');
                target_classes_header[i].classList.remove('container_header');
            }

            for (i = 0; i < target_classes_content.length; i++) {
                target_classes_content[i].classList.add('container_content_kde');
                target_classes_content[i].classList.remove('container_content');
            }
            
            keys = [];    // Reset input keys
        };
    }, true);
};
