$(document).ready(function() {
    // State dictionary.
    var state = {
        up : false,
        down : false,
        left : false,
        right : false
    };

    // Initialize car.
    $.ajax({
        url : '/control.php?initialize',
        dataType : 'json',
        success : function(data) {
            console.log(data);
        }
    });

    /**
     * Send a request to update motor driver's control pins.
     *
     * @param  state  Dictionary with four boolean keys: up, down, left, and right.
     */
    var updateState = function(state) {
        // Motor states.
        var frontMotor = 'neutral';
        var backMotor = 'neutral';

        if (state['left'] == true && state['right'] == false) {
            frontMotor = 'left';
        }
        else if (state['left'] == false && state['right'] == true) {
            frontMotor = 'right';
        }

        if (state['up'] == true && state['down'] == false) {
            backMotor = 'forward';
        }
        else if (state['up'] == false && state['down'] == true) {
            backMotor = 'backward';
        }

        // Request URL.
        var requestURL = '/control.php?';
        requestURL += 'front=' + frontMotor + '&';
        requestURL += 'back=' + backMotor;

        // Send request.
        $.ajax({
            url : requestURL,
            dataType : 'json',
            success : function(data) {
                console.log(data);
            }
        });
    }

    // Keydown event handler.
    $(document).keydown(function(e) {
        var stateChanged = false;

        var keys = {};
        keys[e.which] = true;

        if (keys[87] || keys[38]) {
            // User pressed W or Up Arrow.
            state['up'] = true;
            stateChanged = true;
        }
        if (keys[83] || keys[40]) {
            // User pressed S or Down Arrow.
            state['down'] = true;
            stateChanged = true;
        }
        if (keys[65] || keys[37]) {
            // User pressed A or Left Arrow.
            state['left'] = true;
            stateChanged = true;
        }
        if (keys[68] || keys[39]) {
            // User pressed D or Right Arrow.
            state['right'] = true;
            stateChanged = true;
        }

        if (stateChanged) {
            e.preventDefault();
            updateState(state);
        }
    });

    // Keyup event handler.
    $(document).keyup(function(e) {
        var stateChanged = false;

        var keys = {};
        keys[e.which] = true;

        if (keys[87] || keys[38]) {
            // User released W or Up Arrow.
            state['up'] = false;
            stateChanged = true;
        }
        if (keys[83] || keys[40]) {
            // User released S or Down Arrow.
            state['down'] = false;
            stateChanged = true;
        }
        if (keys[65] || keys[37]) {
            // User released A or Left Arrow.
            state['left'] = false;
            stateChanged = true;
        }
        if (keys[68] || keys[39]) {
            // User released D or Right Arrow.
            state['right'] = false;
            stateChanged = true;
        }

        if (stateChanged) {
            e.preventDefault();
            updateState(state);
        }
    });
});
