<?php

class Alert
{
    private static $sessionKey = 'sweet-alert';

    /**
     * Set a SweetAlert2 flash message using named parameters.
     *
     * @param array $options Associative array of alert options.
     *  - title (string): Title of the alert.
     *  - text (string): Body text of the alert.
     *  - icon (string): One of success, error, warning, info, question.
     *  - timer (int): Milliseconds before auto-dismiss (ignored if showConfirmButton is true).
     *  - showConfirmButton (bool): Whether to show the confirm button.
     *  - path (string|null): Redirect path after setting the alert.
     */
    public static function flash($options = array())
    {
        $defaults = array(
            'timer'             => isset($options['timer']) ? $options['timer'] : 2500,
            'showConfirmButton' => false,
            'path'              => null,
        );

        $config = array_merge($defaults, $options);

        // If confirm button is shown, disable auto-dismiss
        if ($config['showConfirmButton']) {
            unset($config['timer']);
        }

        // Set the alert configuration in the session without the path
        $alertConfig = $config;
        unset($alertConfig['path']);
        $_SESSION[self::$sessionKey] = $alertConfig;

        // If a path is provided, redirect after setting the alert
        $path = isset($config['path']) ? $config['path'] : $_SERVER['PHP_SELF'];
        if (!empty($path)) {
            safe_redirect($path);
        }
    }

    public static function success($options = array())
    {
        $options['icon'] = 'success';
        self::flash($options);
    }

    public static function info($options = array())
    {
        $options['icon'] = 'info';
        self::flash($options);
    }

    public static function warning($options = array())
    {
        $options['icon'] = 'warning';
        self::flash($options);
    }

    public static function error($options = array())
    {
        $options['icon'] = 'error';
        self::flash($options);
    }

    /**
     * Render the SweetAlert2 alert, then clear the session.
     */
    public static function render()
    {
        if (!isset($_SESSION[self::$sessionKey]))
            return;

        $alert = $_SESSION[self::$sessionKey];
        echo '<script>
            setTimeout(() => {
                Swal.fire(' . json_encode($alert) . ');
            }, 500);
        </script>';
        unset($_SESSION[self::$sessionKey]);
    }
}