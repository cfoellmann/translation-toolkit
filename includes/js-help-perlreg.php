'<table class="widefat" cellpadding="3" cellspacing="0" border="1">'+
'<thead>'+
'<tr>'+
'<th nowrap="nowrap"><?php echo esc_js(_x('Component','pcre','translation-toolkit')) ?></th>'+
'<th nowrap="nowrap"><?php echo esc_js(_x('Example','pcre','translation-toolkit')) ?></th>'+
'<th nowrap="nowrap"><?php echo esc_js(_x('Description','pcre','translation-toolkit')) ?></th>'+
'</tr>'+
'</thead>'+
'<tbody>'+
'<tr>'+
'<td nowrap="nowrap">&nbsp;</td>'+
'<td nowrap="nowrap">/<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>/</td>'+
'<td><?php echo esc_js(_x('matches "out", but also "timeout", "outbreak", "Route" and "gouty".','pcre','translation-toolkit')); ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">^</td>'+
'<td nowrap="nowrap">/^<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>/</td>'+
'<td><?php echo esc_js(_x('matches "out" at start of string like "out", "outbreak", as long as this are the first words at string.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">$</td>'+
'<td nowrap="nowrap">/<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>$/</td>'+
'<td><?php echo esc_js(_x('matches "out" at end of string like "out", "timeout" and "burnout" as long as this are the last words at string.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">*</td>'+
'<td nowrap="nowrap">/<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>*/</td>'+
'<td><?php echo esc_js(_x('matches "ou", "out", "outt" and "outttttt", the char prior to asterisk can be repeated 0 to unlimited times.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">+</td>'+
'<td nowrap="nowrap">/<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>+/</td>'+
'<td><?php echo esc_js(_x('matches "outt" and "outttt", the char prior to plus char have to be repeated at least one time or more often.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">.</td>'+
'<td nowrap="nowrap">/.<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>/</td>'+
'<td><?php echo esc_js(_x('matches "rout" and "gout", any char can be placed at this position.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">.+</td>'+
'<td nowrap="nowrap">/.+<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>/</td>'+
'<td><?php echo esc_js(_x('matches "timeout" and "Fallout", any char sequence at this position. Is a combination of any char and 1 but upto many times.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\b</td>'+
'<td nowrap="nowrap">/\\b<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>\\b/</td>'+
'<td><?php echo esc_js(_x('matches "out" as single word. \b means word break.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\B</td>'+
'<td nowrap="nowrap">/\\B<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>\\B/</td>'+
'<td><?php echo esc_js(_x('matches "out" only inside words, like "Route" or "gouty". \B means not word break.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\d</td>'+
'<td nowrap="nowrap">/\\d+/</td>'+
'<td><?php echo esc_js(_x('matches any number. \d means a numerical digit (0 to 9)','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\D</td>'+
'<td nowrap="nowrap">/\\D+/</td>'+
'<td><?php echo esc_js(_x('matches "-out" at "3-out", any non number.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\f</td>'+
'<td nowrap="nowrap">/\\f/</td>'+
'<td><?php echo esc_js(_x('matches form feed char.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\n</td>'+
'<td nowrap="nowrap">/\\n/</td>'+
'<td><?php echo esc_js(_x('matches line feed char.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\r</td>'+
'<td nowrap="nowrap">/\\r/</td>'+
'<td><?php echo esc_js(_x('matches carriage return char.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\t</td>'+
'<td nowrap="nowrap">/\\t/</td>'+
'<td><?php echo esc_js(_x('matches tabulator char.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\v</td>'+
'<td nowrap="nowrap">/\\v/</td>'+
'<td><?php echo esc_js(_x('matches vertical tabulator char.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\s</td>'+
'<td nowrap="nowrap">/\\s/</td>'+
'<td><?php echo esc_js(_x('matches any kind of whitespace and space char.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\S</td>'+
'<td nowrap="nowrap">/\\S+/</td>'+
'<td><?php echo esc_js(_x('matches any char, that is not a whitespace char.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\w</td>'+
'<td nowrap="nowrap">/\\w+/</td>'+
'<td><?php echo esc_js(_x('matches any alphanumerical char and underscore (typical for programming syntax).','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">\\W</td>'+
'<td nowrap="nowrap">/\\W/</td>'+
'<td><?php echo esc_js(_x('matches any char, that is not alphanumerical char and underscore (typical for illegal char detection at programming).','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">()</td>'+
'<td nowrap="nowrap">/(<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>)/</td>'+
'<td><?php echo esc_js(_x('matches "out" and remembers matches internally. Upto 9 brackets are allowed per expression.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">/.../g</td>'+
'<td nowrap="nowrap">/<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>/g</td>'+
'<td><?php echo esc_js(_x('matches "aus" as often it is contained at string. The match positions will be stored internally as array.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">/.../i</td>'+
'<td nowrap="nowrap">/<?php echo esc_js(_x('out','pcre','translation-toolkit')) ?>/i</td>'+
'<td><?php echo esc_js(_x('matches "out", "Out" and "OUT", not case-sensitive match.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'<tr>'+
'<td nowrap="nowrap">/.../gi</td>'+
'<td nowrap="nowrap">/<?php echo _x('out','pcre','translation-toolkit') ?>/gi</td>'+
'<td><?php echo esc_js(_x('matches "out", as ofter occurs (g) and also non case-sensitive.','pcre','translation-toolkit')) ?></td>'+
'</tr>'+
'</tbody>'+
'</table>'+
