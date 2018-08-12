<?php
global $myhome_estate;
?>
<div id="mh_rev_gallery_single_wrapper"
     class="rev_slider_wrapper fullwidthbanner-container"
     data-alias="single-estate-gallery">
	<div id="mh_rev_gallery_single"
         class="rev_slider fullwidthabanner" data-version="5.3.0">
		<ul class="mh-popup-group">
			<?php
			if ( $myhome_estate->has_gallery() ) :
				foreach ( $myhome_estate->get_gallery() as $myhome_key => $myhome_image ) : ?>
					<li data-index="rs-<?php echo esc_attr( $myhome_key ); ?>"
                        data-transition="parallaxhorizontal"
                        data-slotamount="default"
                        data-hideafterloop="0"
                        data-hideslideonmobile="off"
                        data-easein="default"
                        data-easeout="default"
                        data-masterspeed="500"
                        data-thumb="<?php echo esc_url( $myhome_image['url'] ); ?>"
                        data-rotate="0"
                        data-fsslotamount="7"
                        data-saveperformance="off"
					<?php if ( ! $myhome_key ) : ?>
						data-fstransition="fade"
						data-fsmasterspeed="300"
					<?php endif; ?>
					>
						<img src="<?php echo esc_url( $myhome_image['url'] ); ?>"
                             data-sizes="auto"
                             alt="<?php echo esc_attr( $myhome_estate->get_name() ); ?>"
                             title=""
                             width="1920"
                             height="1240"
                             data-bgposition="center center"
                             data-bgfit="cover"
                             data-bgrepeat="no-repeat"
                             class="rev-slidebg lazyload"
                             data-no-retina>
						<a href="<?php echo esc_url( $myhome_image['url'] ); ?>" class="mh-popup-group__element">
							<div class="tp-caption tp-shape tp-shapewrapper  tp-resizeme"
								 data-x="['center','center','center','center']" data-hoffset="['2','2','0','0']"
								 data-y="['middle','middle','middle','middle']" data-voffset="['0','0','0','0']"
								 data-width="full"
								 data-height="full"
								 data-whitespace="normal"
								 data-type="shape"
								 data-basealign="slide"
								 data-responsive_offset="on"
								 data-frames='[{"delay":0,"speed":300,"frame":"0","from":"opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"wait","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
								 data-textAlign="['inherit','inherit','inherit','inherit']"
								 data-paddingtop="[0,0,0,0]"
								 data-paddingright="[0,0,0,0]"
								 data-paddingbottom="[0,0,0,0]"
								 data-paddingleft="[0,0,0,0]"></div>
						</a>

					</li>
				<?php
                endforeach;
			endif;
			?>
		</ul>
    </div>
</div>