/**
* Creates an alert div element
* @param {String} message
* @param {String} status
* @param {Integer} timeout
* @return {Element} sum
*/
function createAlert(message, status, timeout) {

	//Used to determine whether to remove setTimeout or not
    var timeout_check;

    //Create alert element
    var alert = document.createElement("div");
    alert.className += "animation-target anb ";

    //Attach correct colour to alert
    var status_class = "alert-" + status + " ";
    alert.className += status_class;

    //Create close button
    var close_button = document.createElement("span");
    close_button.className += " close-alert-x fa fa-remove";

    /*
        There are 3 event listeners:
            1. Clicking x to close alert
            2. Mousing over to prevent timeout
            3. Mousing out to start timeout
    */
    close_button.addEventListener("click", function() {
        var parent = this.parentNode;
        parent.parentNode.removeChild(parent);
    });

    alert.addEventListener("mouseover", function() {
        this.classList.remove("fade-out");
        clearTimeout(timeout_check);
    });

    alert.addEventListener("mouseout", function() {
        timeout_check = setTimeout(function() {
            var parent = alert.parent;
            alert.className += " fade-out";
            if (alert.parentNode) {
                timeout_check = setTimeout(function() {
                    alert.parentNode.removeChild(alert)
                }, 500);
            }
        }, 3000);
    });

    //Add message and close button
    alert.innerHTML = message;
    alert.appendChild(close_button);

    //Prepend new alert to container
    var alert_wrapper = document.getElementById("anb-wrapper");
    alert_wrapper.insertBefore(alert, alert_wrapper.children[0]);

    //If they haven't clicked close within the timeout period, fade out and remove element
    timeout_check = setTimeout(function() {
        var parent = alert;
        parent.className += " fade-out";
        if (parent.parentNode) {
            timeout_check = setTimeout(function() {
                parent.parentNode.removeChild(parent)
            }, 500);
        }
    }, timeout);
};