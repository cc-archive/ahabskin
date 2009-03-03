/**
* Search_Box behavior
* @author   zytzagoo
* @version  0.2
* @license  http://www.opensource.org/licenses/mit-license.php
*/
function Search_Box(cfg) {
    // defaults are always nice
    var defaults = { ELEMENT_ID: 'q', DEFAULT_VALUE: 'inherit', FOCUSED_VALUE: '' };
    if (cfg) {
        // we have a cfg, loop thru the properties
        // and make sure something is not missing
        // if so, add it from the defaults
        for (var name in cfg) {
            if (cfg.hasOwnProperty(name)) {
                for (var defname in defaults) {
                    if (defaults.hasOwnProperty(defname)) {
                        if (!(cfg[defname])) {
                            cfg[defname] = defaults[defname];
                        }
                    }
                }
            }
        }
    } else {
        cfg = defaults;
    }
    /**
    * return a new object literal with extra
    * stuff attached on it
    */
    return {
        /**
        * Checks the element we're working on exists, and
        * attaches handlers to it. Usually called after the document
        * has loaded or (even better but harder to achieve truly
        * cross-browser) when the element referenced by
        * Search_Input.ELEMENT_ID is available in the DOM.
        */
        init: function () {
            var el = document.getElementById(cfg.ELEMENT_ID);
            if (el) {
                /**
                * special case: 'inherit'
                * This resets the passed in default value and
                * if the element has a previously set value, that
                * value is used as the default from now on.
                */
                if (cfg.DEFAULT_VALUE === 'inherit') {
                    cfg.DEFAULT_VALUE = '';
                    if (el.value !== '') {
                        cfg.DEFAULT_VALUE = el.value;
                    }
                }
                /**
                * If a default value is specified, override
                * whatever exists in the value attribute of the input in html
                */

                /**
                * if we have a custom focus handler passed in,
                * attach that one too and make sure it is called first
                */
                if (cfg.focus) {
                    Search_Box.attach_handler(el, 'onfocus', cfg.focus);
                }
                // our own focus handler is always attached
                Search_Box.attach_handler(el, 'onfocus', this.focus);
                /**
                * same as above except this takes care of onblur handlers
                */
                if (cfg.blur) {
                    Search_Box.attach_handler(el, 'onblur', cfg.blur);
                }
                // our own onblur handler is also always attached
                Search_Box.attach_handler(el, 'onblur', this.blur);
                /**
                * in case the elem has no current value,
                * set it to the specified default
                */
                if (el.value === '' || (cfg.DEFAULT_VALUE && cfg.DEFAULT_VALUE !== '')) {
                    el.value = cfg.DEFAULT_VALUE;
                }
            } else {
                throw new Error('Search_Box.init: element (id: "' + cfg.ELEMENT_ID + '") doesn\'t exist');
            }
        },
        /**
        * Handles the onfocus event of the element
        */
        focus: function (e) {
            // delegate, passing in the event object
            var t = Search_Box.get_target(e);
            // if the target of the event is an input element
            if (t.nodeName.toLowerCase() === 'input') {
                // if the value of that input is empty or default
                if (t.value === cfg.DEFAULT_VALUE || t.value === '') {
                    // set the value to the specified focused value
                    t.value = cfg.FOCUSED_VALUE;
                    /**
                    * if the now set focused value is not empty
                    * select the contents of the box
                    */
                    if (t.value !== '') {
                        t.select();
                    }
                }
            }
            return true;
        },
        /**
        * Handles the onblur event of the element
        */
        blur: function (e) {
            // delegate, passing in the event object!
            var t = Search_Box.get_target(e);
            // if the target of the event is an input element
            if (t.nodeName.toLowerCase() === 'input') {
                /**
                * if the current value of that element is the
                * focused value or empty, set the current
                * value to the specified default value
                */
                if (t.value === cfg.FOCUSED_VALUE || t.value === '') {
                    t.value = cfg.DEFAULT_VALUE;
                }
            }
            return true;
        }
    };
}
/**
* Gets the target of an event
*/
Search_Box.get_target = function (x) {
    x = x || window.event;
    return x.target || x.srcElement;
};
/**
* Attaches event handlers to an existing object.
* If an existing handler is found, it is executed before
* our newly attached handler
*/
Search_Box.attach_handler = function (o, evt, f) {
    if (o !== null) {
        var existing_handler = o[evt];
        if (typeof o[evt] !== 'function') {
            /**
            * no previous handler found
            * TODO: this might need looking into,
            * but it seems to work so far...
            */
            o[evt] = f;
        } else {
            /**
            * Previous handler found, invoke it,
            * while making sure that the 'this' keyword
            * inside the handler function refers to the
            * input element.
            * This enables some cool custom onfocus and
            * onblur handlers possible and logical and
            * easy to develop and not worry about naming
            * stuff...
            */
            o[evt] = function (e) {
                existing_handler.apply(o, arguments);
                f.apply(o, arguments);
            };
        }
    }
};