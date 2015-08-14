<?php
$all = file_get_contents($argv[1]);
define(OUTPUT_JSON,($argv[2] == '--output-json'));
define(OUTPUT_DATA,!OUTPUT_JSON);    

// remove comments
// I kept finding a */.some-class in my testing
// credit to http://blog.ostermiller.org/find-comment
$all = preg_replace('/\/\*([^*]|[\r\n\s]|(\*+([^*\/]|[\r\n\s])))*\*+/ms', '', $all);
// turns out it's an actual typo in my test file

// weird shit related to filters ${iefactor} ???
$all = preg_replace('/\$\{.*?\}/ms', '', $all);

// remove @media
$all = preg_replace('/@media[^\{]*?\{/mis', '', $all);
$all = preg_replace('/\}\s*\}/ms', '}', $all);

// destroy all remaining at-rules which break everything
// nested such as @font-face and @keyframes
preg_match_all('/@(-[a-z]+?-)?([a-z-]+)\{(.*?)\}/mis', $all, $match, PREG_SET_ORDER);
foreach($match as $m) $all = str_replace($m[0],'',$all);
// and shit like @import and @charset
$all = preg_replace('/@[a-z0-9-_]+.*?;/ms','', $all);

// grab actual rules
preg_match_all('/([^\{]+?)\s*(\{.*?\})/mis',$all, $match,PREG_SET_ORDER);

// BEGIN DEFINITIONS
$pseudo_elements = [
    'after',
    'before',
    'first-letter',
    'first-line',
    'selection',
    'backdrop',

    /** more standards coming soon maybe? realworldvalidator.com/css/pseudoelements*/
    'alternate',
    'content',
    'cue',
    'cue-region',
    'line-marker',
    'marker',
    'outside',
    /** some vendor-prefix from above source */
    '-moz-focus-inner',
    '-moz-focus-outer',
    '-moz-list-bullet',
    '-moz-list-number',
    '-moz-placeholder',
    '-moz-progress-bar',
    '-moz-selection',
    '-ms-backdrop',
    '-ms-browse',
    '-ms-check',
    '-ms-clear',
    '-ms-expand',
    '-ms-fill',
    '-ms-fill-lower',
    '-ms-fill-upper',
    '-ms-reveal',
    '-ms-thumb',
    '-ms-ticks-after',
    '-ms-ticks-before',
    '-ms-tooltip',
    '-ms-track',
    '-ms-value',
    '-webkit-calendar-picker-indicator',
    '-webkit-color-datetime-edit',
    '-webkit-color-datetime-edit-day-field',
    '-webkit-color-datetime-edit-fields-wrapper',
    '-webkit-color-datetime-edit-month-field',
    '-webkit-color-datetime-edit-text',
    '-webkit-color-datetime-edit-year-field',
    '-webkit-color-swatch',
    '-webkit-color-swatch-wrapper',
    '-webkit-file-upload-button',
    '-webkit-inner-spin-button',
    '-webkit-input-placeholder',
    '-webkit-keygen-select',
    '-webkit-meter-bar',
    '-webkit-meter-even-less-good-value',
    '-webkit-meter-optimum-value',
    '-webkit-meter-suboptimal-value',
    '-webkit-ouer-spin-button',
    '-webkit-progress-bar',
    '-webkit-progress-inner-element',
    '-webkit-progress-value',
    '-webkit-resizer',
    '-webkit-scrollbar',
    '-webkit-scrollbar-button',
    '-webkit-scrollbar-corner',
    '-webkit-scrollbar-thumb',
    '-webkit-scrollbar-track',
    '-webkit-scrollbar-track-piece',
    '-webkit-search-cancel-button',
    '-webkit-search-decoration',
    '-webkit-search-results-button',
    '-webkit-search-results-decoration',
    '-webkit-slider-runnable-track',
    '-webkit-slider-thumb',
    '-webkit-textfield-decoration-container',
    '-webkit-validation-bubble',
    '-webkit-validation-bubble-arrow',
    '-webkit-validation-bubble-arrow-clipper',
    '-webkit-validation-bubble-heading',
    '-webkit-validation-bubble-message',
    '-webkit-validation-bubble-text-block',

    /* g-giddy up https://gist.github.com/afabbro/3759334#gistcomment-716299 */
    '-webkit-calendar-picker-indicator',
    '-webkit-color-swatch',
    '-webkit-color-swatch-wrapper',
    '-webkit-details-marker',
    '-webkit-file-upload-button',
    '-webkit-image-inner-element',
    '-webkit-inner-spin-button',
    '-webkit-input-placeholder',
    '-webkit-input-speech-button',
    '-webkit-keygen-select',
    '-webkit-media-controls-panel',
    '-webkit-media-controls-timeline-container',
    '-webkit-media-slider-container',
    '-webkit-meter-bar',
    '-webkit-meter-even-less-good-value',
    '-webkit-meter-optimum-value',
    '-webkit-meter-suboptimal-value',
    '-webkit-progress-bar',
    '-webkit-progress-value',
    '-webkit-resizer',
    '-webkit-resizer:window-inactive',
    '-webkit-scrollbar',
    '-webkit-scrollbar-button',
    '-webkit-scrollbar-button:disabled',
    '-webkit-scrollbar-button:double-button:horizontal:end:decrement',
    '-webkit-scrollbar-button:double-button:horizontal:end:increment',
    '-webkit-scrollbar-button:double-button:horizontal:end:increment:corner-present',
    '-webkit-scrollbar-button:double-button:horizontal:start:decrement',
    '-webkit-scrollbar-button:double-button:horizontal:start:increment',
    '-webkit-scrollbar-button:double-button:vertical:end:decrement',
    '-webkit-scrollbar-button:double-button:vertical:end:increment',
    '-webkit-scrollbar-button:double-button:vertical:end:increment:corner-present',
    '-webkit-scrollbar-button:double-button:vertical:start:decrement',
    '-webkit-scrollbar-button:double-button:vertical:start:increment',
    '-webkit-scrollbar-button:end',
    '-webkit-scrollbar-button:end:decrement',
    '-webkit-scrollbar-button:end:increment',
    '-webkit-scrollbar-button:horizontal',
    '-webkit-scrollbar-button:horizontal:decrement',
    '-webkit-scrollbar-button:horizontal:decrement:active',
    '-webkit-scrollbar-button:horizontal:decrement:hover',
    '-webkit-scrollbar-button:horizontal:decrement:window-inactive',
    '-webkit-scrollbar-button:horizontal:end',
    '-webkit-scrollbar-button:horizontal:end:decrement',
    '-webkit-scrollbar-button:horizontal:end:increment',
    '-webkit-scrollbar-button:horizontal:end:increment:corner-present',
    '-webkit-scrollbar-button:horizontal:increment',
    '-webkit-scrollbar-button:horizontal:increment:active',
    '-webkit-scrollbar-button:horizontal:increment:hover',
    '-webkit-scrollbar-button:horizontal:increment:window-inactive',
    '-webkit-scrollbar-button:horizontal:start',
    '-webkit-scrollbar-button:horizontal:start:decrement',
    '-webkit-scrollbar-button:horizontal:start:increment',
    '-webkit-scrollbar-button:start',
    '-webkit-scrollbar-button:start:decrement',
    '-webkit-scrollbar-button:start:increment',
    '-webkit-scrollbar-button:vertical',
    '-webkit-scrollbar-button:vertical:decrement',
    '-webkit-scrollbar-button:vertical:decrement:active',
    '-webkit-scrollbar-button:vertical:decrement:hover',
    '-webkit-scrollbar-button:vertical:decrement:window-inactive',
    '-webkit-scrollbar-button:vertical:end',
    '-webkit-scrollbar-button:vertical:end:decrement',
    '-webkit-scrollbar-button:vertical:end:increment',
    '-webkit-scrollbar-button:vertical:end:increment:corner-present',
    '-webkit-scrollbar-button:vertical:increment',
    '-webkit-scrollbar-button:vertical:increment:active',
    '-webkit-scrollbar-button:vertical:increment:hover',
    '-webkit-scrollbar-button:vertical:increment:window-inactive',
    '-webkit-scrollbar-button:vertical:start',
    '-webkit-scrollbar-button:vertical:start:decrement',
    '-webkit-scrollbar-button:vertical:start:increment',
    '-webkit-scrollbar-corner',
    '-webkit-scrollbar-corner:window-inactive',
    '-webkit-scrollbar-thumb',
    '-webkit-scrollbar-thumb:horizontal',
    '-webkit-scrollbar-thumb:horizontal:active',
    '-webkit-scrollbar-thumb:horizontal:hover',
    '-webkit-scrollbar-thumb:horizontal:window-inactive',
    '-webkit-scrollbar-thumb:vertical',
    '-webkit-scrollbar-thumb:vertical:active',
    '-webkit-scrollbar-thumb:vertical:hover',
    '-webkit-scrollbar-thumb:vertical:window-inactive',
    '-webkit-scrollbar-track',
    '-webkit-scrollbar-track-piece',
    '-webkit-scrollbar-track-piece:disabled',
    '-webkit-scrollbar-track-piece:end',
    '-webkit-scrollbar-track-piece:horizontal:decrement',
    '-webkit-scrollbar-track-piece:horizontal:decrement:active',
    '-webkit-scrollbar-track-piece:horizontal:decrement:hover',
    '-webkit-scrollbar-track-piece:horizontal:end',
    '-webkit-scrollbar-track-piece:horizontal:end:corner-present',
    '-webkit-scrollbar-track-piece:horizontal:end:double-button',
    '-webkit-scrollbar-track-piece:horizontal:end:no-button',
    '-webkit-scrollbar-track-piece:horizontal:end:no-button:corner-present',
    '-webkit-scrollbar-track-piece:horizontal:end:single-button',
    '-webkit-scrollbar-track-piece:horizontal:increment',
    '-webkit-scrollbar-track-piece:horizontal:increment:active',
    '-webkit-scrollbar-track-piece:horizontal:increment:hover',
    '-webkit-scrollbar-track-piece:horizontal:start',
    '-webkit-scrollbar-track-piece:horizontal:start:double-button',
    '-webkit-scrollbar-track-piece:horizontal:start:no-button',
    '-webkit-scrollbar-track-piece:horizontal:start:single-button',
    '-webkit-scrollbar-track-piece:start',
    '-webkit-scrollbar-track-piece:vertical:decrement',
    '-webkit-scrollbar-track-piece:vertical:decrement:active',
    '-webkit-scrollbar-track-piece:vertical:decrement:hover',
    '-webkit-scrollbar-track-piece:vertical:end',
    '-webkit-scrollbar-track-piece:vertical:end:corner-present',
    '-webkit-scrollbar-track-piece:vertical:end:double-button',
    '-webkit-scrollbar-track-piece:vertical:end:no-button',
    '-webkit-scrollbar-track-piece:vertical:end:no-button:corner-present',
    '-webkit-scrollbar-track-piece:vertical:end:single-button',
    '-webkit-scrollbar-track-piece:vertical:increment',
    '-webkit-scrollbar-track-piece:vertical:increment:active',
    '-webkit-scrollbar-track-piece:vertical:increment:hover',
    '-webkit-scrollbar-track-piece:vertical:start',
    '-webkit-scrollbar-track-piece:vertical:start:double-button',
    '-webkit-scrollbar-track-piece:vertical:start:no-button',
    '-webkit-scrollbar-track-piece:vertical:start:single-button',
    '-webkit-scrollbar-track:disabled',
    '-webkit-scrollbar-track:horizontal',
    '-webkit-scrollbar-track:horizontal:disabled',
    '-webkit-scrollbar-track:horizontal:disabled:corner-present',
    '-webkit-scrollbar-track:vertical:disabled',
    '-webkit-scrollbar-track:vertical:disabled:corner-present',
    '-webkit-scrollbar:horizontal',
    '-webkit-scrollbar:horizontal:corner-present',
    '-webkit-scrollbar:horizontal:window-inactive',
    '-webkit-scrollbar:vertical',
    '-webkit-scrollbar:vertical:corner-present',
    '-webkit-scrollbar:vertical:window-inactive',
    '-webkit-search-cancel-button',
    '-webkit-search-decoration',
    '-webkit-search-results-button',
    '-webkit-search-results-decoration',
    '-webkit-slider-container',
    '-webkit-slider-runnable-track',
    '-webkit-slider-thumb',
    '-webkit-slider-thumb:disabled',
    '-webkit-slider-thumb:hover',
    '-webkit-textfield-decoration-container',
    '-webkit-validation-bubble',
    '-webkit-validation-bubble-arrow',
    '-webkit-validation-bubble-arrow-clipper',
    '-webkit-validation-bubble-heading',
    '-webkit-validation-bubble-message',
    '-webkit-validation-bubble-text-block',
    '-moz-anonymous-block',
    '-moz-anonymous-positioned-block',

    /** y-you too https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Mozilla_Extensions **/
    '-moz-canvas',
    '-moz-cell-content',
    '-moz-focus-inner',
    '-moz-focus-outer',
    '-moz-inline-table',
    '-moz-viewport',
    '-moz-viewport-scroll',
    '-moz-xul-anonymous-block'
];
// Probably some duplicates in this list.
# idgaf
// END DEFINITIONS

$out = [];
$pos = 1;
foreach($match as $m) {
    $selectors_list = explode(',', trim($m[1]));
    if(OUTPUT_JSON) {
        $important = preg_match_all('/!important/i', $m[2]);
    }
    foreach($selectors_list as $selector) {
        // NOTE: sometimes there's random junk at the start of the selectors and I don't know why fgsfds
        $selector = trim(preg_replace('/^[^a-z0-9-_\[\*\#\+~\>\:\.\s]+/mis', '', $selector));
        if($selector == "")
            continue;
        $a = $b = $c = 0;
        $original_selector = $selector;

        /** NOTE: attribute selectors can contain quoted strings which can fuck this all up
         * e.g. a[href^="#"] triggers the dumb ID check below.
         * so lets just get rid of them first. */
        // attributes
        $at = 0;
        if(strpos($selector,'[') !== false) {
            $at = preg_match_all('/\[.*?\]/ms', $selector,$match);
            foreach($match as $m) $selector = str_replace($m[0],'',$selector);
        }
        // ids
        $id = 0;
        $id_pos = -1;
        while(($id_pos = strpos($selector,'#',$id_pos+1)) !== false) $id++;
        
        // classes
        $cl = 0;
        $cl_pos = -1;
        while(($cl_pos = strpos($selector,'.',$cl_pos+1)) !== false) $cl++;

        // pseudo
        $ps_cl = 0;
        $ps_el = 0;
        $ps_pos = 0;
        $pseudoers = $selector;
        while(strlen($pseudoers)) {
            if(preg_match('/:{1,2}([a-z0-9-]+)/ims', $pseudoers, $n)) {
                $pseudo = $n[1];
    /**
     * Selectors inside the negation pseudo-class are counted like any other, 
     * but the negation itself does not count as a pseudo-class. 
     */
                if($pseudo != 'not') {
                    if(in_array($pseudo,$pseudo_elements))
                        $ps_el++;
                    else
                        $ps_cl++;
                    // see below note on $pseudo_classes
                }
                $pseudoers = str_replace($n[0],'',$pseudoers);
            } else {
                break;
            }
        }

        // elements
        $el = 0;
        $elements = trim(preg_replace('/(\*|\>|~|\+)|((#|\.)[a-z0-9-_]+)|(\[.*?\])|(:{1,2}([a-z0-9-]+(\(.*?\))?))/ims', ' ', $selector));
        $el = $elements == "" ? 0 : count(preg_split('/\s+/m',$elements));
/**
 *      9. Calculating a selector's specificity
 *
 *      A selector's specificity is calculated as follows:
 *
 *          count the number of ID selectors in the selector (= a)
 *          count the number of class selectors, attributes selectors, and pseudo-classes in the selector (= b)
 *          count the number of type selectors and pseudo-elements in the selector (= c)
 *          ignore the universal selector 
 *
 */
        $a = $id;
        $b = $cl + $at + $ps_cl;
        $c = $el + $ps_el;
/*
 *      Concatenating the three numbers a-b-c (in a number system with a large base) gives the specificity. 
 */
        $base36_a = base_convert($a, 10, 36);
        $base36_b = base_convert($b, 10, 36);
        $base36_c = base_convert($c, 10, 36);
        $base36 = $base36_a.$base36_b.$base36_c;
        $base10 = base_convert($base36, 36, 10);

        if(OUTPUT_JSON) {
            $out[] = [
                'selector'    => preg_replace('/\s+/',' ',trim($original_selector)),
                'specificity' => [
                        'base36'  => $base36,
                        'base10'  => $base10,
                        'tuple'   => [0,$a,$b,$c]
                ],
                'position'    => $pos,
                'important'   => $important
//                'elements-debug' => $elements,
//                'a-b-c-debug'=>[$a,$b,$c]
            ];
        } else {
            $out[] = "$pos $base10";
        }
    }
    $pos++; // increment here, not per comma-separated-value
}

echo OUTPUT_JSON ? json_encode($out, JSON_PRETTY_PRINT) : implode("\n", $out);
exit(0);

/**
 * also from https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Mozilla_Extensions 
 * but unused because this is taking too long already and I can probably 
 * assume if it's not in the above list of pseudo element that  it's a pseudo class
 */
$pseudo_classes = [
    '-moz-any',
    '-moz-any-link',
    '-moz-bound-element',
    '-moz-broken',
    '-moz-drag-over',
    '-moz-first-node',
    '-moz-focusring',
    '-moz-full-screen',
    '-moz-full-screen-ancestor',
    '-moz-handler-blocked',
    '-moz-handler-crashed',
    '-moz-handler-disabled',
    '-moz-last-node',
    '-moz-list-bullet',
    '-moz-list-number',
    '-moz-loading',
    '-moz-locale-dir(ltr)',
    '-moz-locale-dir(rtl)',
    '-moz-lwtheme',
    '-moz-lwtheme-brighttext',
    '-moz-lwtheme-darktext',
    '-moz-native-anonymous Requires Gecko 36',
    '-moz-only-whitespace',
    '-moz-placeholder',
    '-moz-read-only',
    '-moz-read-write',
    '-moz-suppressed',
    '-moz-submit-invalid',
    '-moz-system-metric(images-in-menus)',
    '-moz-system-metric(mac-graphite-theme)',
    '-moz-system-metric(scrollbar-end-backward)',
    '-moz-system-metric(scrollbar-end-forward)',
    '-moz-system-metric(scrollbar-start-backward)',
    '-moz-system-metric(scrollbar-start-forward)',
    '-moz-system-metric(scrollbar-thumb-proportional)',
    '-moz-system-metric(touch-enabled)',
    '-moz-system-metric(windows-default-theme)',
    '-moz-tree-checkbox',
    '-moz-tree-cell',
    '-moz-tree-cell-text',
    '-moz-tree-cell-text(hover)',
    '-moz-tree-column',
    '-moz-tree-drop-feedback',
    '-moz-tree-image',
    '-moz-tree-indentation',
    '-moz-tree-line',
    '-moz-tree-progressmeter',
    '-moz-tree-row',
    '-moz-tree-row(hover)',
    '-moz-tree-separator',
    '-moz-tree-twisty',
    '-moz-ui-invalid',
    '-moz-ui-valid',
    '-moz-user-disabled',
    '-moz-window-inactive'
];
