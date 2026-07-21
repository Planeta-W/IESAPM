<?php
if(is_singular(array('graduacao', 'pos-graduacao', 'extensao'))):

get_template_part('singles/single-curso');

else :

get_template_part('singles/single-default');

endif;
?>
