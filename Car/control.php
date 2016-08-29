<?php
// Always print JSON.
header('Content-Type: text/javascript');
// No caching.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Constants.
const LEFT_PIN = 25;
const RIGHT_PIN = 24;
const FORWARD_PIN = 23;
const BACKWARD_PIN = 22;

/**
 * Get states of front and back motors.
 *
 * @return Array with two entries, 'front' and 'back'. 'front' can be 'left',
 *         'right', or 'neutral'. 'back' can be 'forward', 'backward', or
 *         'neutral'.
 */
function motor_status() {
    // Read pin values.
    $leftPinVal = intval(shell_exec('/usr/local/bin/gpio read ' . LEFT_PIN));
    $rightPinVal = intval(shell_exec('/usr/local/bin/gpio read ' . RIGHT_PIN));
    $forwardPinVal = intval(shell_exec('/usr/local/bin/gpio read ' . FORWARD_PIN));
    $backwardPinVal = intval(shell_exec('/usr/local/bin/gpio read ' . BACKWARD_PIN));

    $status = array();

    // Get status of front motor.
    if (($leftPinVal == 0 && $rightPinVal == 0) || ($leftPinVal == 1 && $rightPinVal == 1)) {
        $status['front'] = 'neutral';
    }
    else if ($leftPinVal == 1 && $rightPinVal == 0) {
        $status['front'] = 'left';
    }
    else {
        $status['front'] = 'right';
    }

    // Get status of back motor.
    if (($forwardPinVal == 0 && $backwardPinVal == 0) || ($forwardPinVal == 1 && $backwardPinVal == 1)) {
        $status['back'] = 'neutral';
    }
    else if ($forwardPinVal == 1 && $backwardPinVal == 0) {
        $status['back'] = 'forward';
    }
    else {
        $status['back'] = 'backward';
    }

    return $status;
}

/**
 * Main function.
 */
function main() {
    // If there are no GET parameters, there's nothing to do.
    if ($_GET) {
        if (isset($_GET['initialize'])) {
            // Initialize pins.
            shell_exec('/usr/local/bin/gpio mode ' . LEFT_PIN . ' out');
            shell_exec('/usr/local/bin/gpio mode ' . RIGHT_PIN . ' out');
            shell_exec('/usr/local/bin/gpio mode ' . FORWARD_PIN . ' out');
            shell_exec('/usr/local/bin/gpio mode ' . BACKWARD_PIN . ' out');

            shell_exec('/usr/local/bin/gpio write ' . LEFT_PIN . ' 0');
            shell_exec('/usr/local/bin/gpio write ' . RIGHT_PIN . ' 0');
            shell_exec('/usr/local/bin/gpio write ' . FORWARD_PIN . ' 0');
            shell_exec('/usr/local/bin/gpio write ' . BACKWARD_PIN . ' 0');

            // Print status and return.
            http_response_code(200);
            echo json_encode(motor_status());
            return;
        }

        if (isset($_GET['status'])) {
            // Print status and return.
            http_response_code(200);
            echo json_encode(motor_status());
            return;
        }

        // Process commands for front motors if needed.
        if (isset($_GET['front'])) {
            switch($_GET['front']) {
                case 'left':
                    // Set front motors to turn left.
                    shell_exec('/usr/local/bin/gpio write ' . LEFT_PIN . ' 1');
                    shell_exec('/usr/local/bin/gpio write ' . RIGHT_PIN . ' 0');
                    break;

                case 'right':
                    // Set front motors to turn right.
                    shell_exec('/usr/local/bin/gpio write ' . LEFT_PIN . ' 0');
                    shell_exec('/usr/local/bin/gpio write ' . RIGHT_PIN . ' 1');
                    break;

                default:
                    // Set front motors to neutral.
                    shell_exec('/usr/local/bin/gpio write ' . LEFT_PIN . ' 0');
                    shell_exec('/usr/local/bin/gpio write ' . RIGHT_PIN . ' 0');
                    break;
            }
        }

        // Process commands for back motors if needed.
        if (isset($_GET['back'])) {
            switch($_GET['back']) {
                case 'forward':
                    // Set back motors to move forward.
                    shell_exec('/usr/local/bin/gpio write ' . FORWARD_PIN . ' 1');
                    shell_exec('/usr/local/bin/gpio write ' . BACKWARD_PIN . ' 0');
                    break;

                case 'backward':
                    // Set back motors to move backward.
                    shell_exec('/usr/local/bin/gpio write ' . FORWARD_PIN . ' 0');
                    shell_exec('/usr/local/bin/gpio write ' . BACKWARD_PIN . ' 1');
                    break;

                default:
                    // Set back motors to neutral.
                    shell_exec('/usr/local/bin/gpio write ' . FORWARD_PIN . ' 0');
                    shell_exec('/usr/local/bin/gpio write ' . BACKWARD_PIN . ' 0');
                    break;
            }
        }

        // Print status and return.
        http_response_code(200);
        echo json_encode(motor_status());
    }
}

main();
?>
