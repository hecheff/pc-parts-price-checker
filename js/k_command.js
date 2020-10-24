if (window.addEventListener) {
    var keys = [], 
        kCommand = "38,38,40,40,37,39,37,39,66,65";

    window.addEventListener("keydown", function(e){
        keys.push(e.keyCode);

        // Run once key inputs fully match the kCommand array entries
        if (keys.toString().indexOf(kCommand) >= 0) {
            alert("WHAT ARE YOU DOING HERE");

            //document.getElementById("kCom").innerHTML = "<embed src='" + "/audio/kCommand.mp3" + "' hidden=true autostart=true loop=false ></embed>";
            /*
            $("html, body").animate({ scrollTop: 0 }, "slow");  // Animate "back to top" scrolling
            document.getElementById("kCom").innerHTML = 
                "<div class='dropdown'>" +
                    "<button class='dropbtn menu_default' title='DEV WORK'>" + 
                        "DEV WORK <i class='fa fa-caret-down'></i>" + 
                    "</button>" + 
                    "<div class='dropdown-content'><a href='/dev/'>DEV STUFF</a></div>" + 
                "</div>";

            $('#kCom:hidden').fadeIn(3000);
            */

            keys = [];    // Reset input keys
        };
    }, true);
};
