<?php
namespace Module\HTMLCompress;

/**
 * Compress the HTML from sleepy_render
 *
 * @return void
 * @internal
 */
function compress($page) {
	return process_data_jmr1($page);
}

/**
 * Regex to safely compress HTML
 * @param  string $text uncompressed HTML
 * @return string       compressed HTML
 * @internal
 */
function process_data_jmr1($text) {
	$re = '%# Collapse whitespace everywhere but in blacklisted elements.
		(?>             # Match all whitespans other than single space.
		  [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
		| \s{2,}        # or two or more consecutive-any-whitespace.
		) # Note: The remaining regex consumes no text at all...
		(?=             # Ensure we are not in a blacklist tag.
		  [^<]*+        # Either zero or more non-"<" {normal*}
		  (?:           # Begin {(special normal*)*} construct
		    <           # or a < starting a non-blacklist tag.
		    (?!/?(?:textarea|pre|script)\b)
		    [^<]*+      # more non-"<" {normal*}
		  )*+           # Finish "unrolling-the-loop"
		  (?:           # Begin alternation group.
		    <           # Either a blacklist start tag.
		    (?>textarea|pre|script)\b
		  | \z          # or end of file.
		  )             # End alternation group.
		)  # If we made it here, we are not in a blacklist tag.
		%Six';
	$text = preg_replace($re, " ", $text);
	if ($text === null) throw \Exception("PCRE Error! File too big.\n");
	return $text;
}

if (\Sleepy\SM::isLive()) {
	\Sleepy\Hook::applyFilter('sleepy_render', '\Module\HTMLCompress\compress');
}