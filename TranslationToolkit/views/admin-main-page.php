<div id="csp-wrap-main" class="wrap">
	<h2><?php _e( 'Manage Language Files', 'translation-toolkit' ); ?></h2>

	<?php if ( CSL_FILESYSTEM_DIRECT !== true ) { ?>
		<div>
			<p class="warning">
				<strong><?php _e( 'File Permission Problem:', 'translation-toolkit' ); ?></strong>
				<?php _e( 'Your WordPress installation does not permit the modification of translation files directly. You will be prompt for FTP credentials if required.', 'translation-toolkit' ); ?>&nbsp;
				<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e( 'What does that mean?', 'translation-toolkit' ); ?>" rel="filepermissions">
					<img src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/question.gif'; ?>" />
				</a>
			</p>
		</div>
	<?php } ?>

	<h3 class="nav-tab-wrapper">
		<?php
		foreach ( $tt_tabs['translation-toolkit'] as $id => $tab ) {
			$class = ( $id == ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'all' ) ? ' nav-tab-active' : '';
			echo '<a href="' . add_query_arg( array( 'page' => 'translation-toolkit', 'tab' => $id  ), admin_url( apply_filters( 'tt_page_parent', 'tools.php' ) ) ) . '" class="nav-tab' . $class . '">' . esc_html( $tab['label'] ) . '</a>';
		}
		?>
	</h3><!-- .nav-tab-wrapper -->

	<table class="wp-list-table widefat plugins" cellspacing="0">
		<thead>
			<tr>
				<th scope="col" class="tt-item-icon"><?php _e( 'Type', 'translation-toolkit' ); ?></th>
				<th scope="col"><?php _e( 'Description', 'translation-toolkit' ); ?></th>
				<th scope="col"><?php _e( 'Languages', 'translation-toolkit' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th scope="col" class="tt-item-icon"><?php _e( 'Type', 'translation-toolkit' ); ?></th>
				<th scope="col"><?php _e( 'Description', 'translation-toolkit' ); ?></th>
				<th scope="col"><?php _e( 'Languages', 'translation-toolkit' ); ?></th>
			</tr>
		</tfoot>
		<tbody id="the-gettext-list">
			<?php
			if ( isset( $_GET['tab'] ) ) {
				$tab = $_GET['tab'];
			} else { // if ( !isset( $_GET['tab'] ) || 'all' == $_GET['tab'] )
				$tab = '';
			}
			$rows = TranslationToolkit_Helpers::get_packages( $tab );

			foreach ( $rows as $data ) {
			?>
			<tr class="tt-item <?php if ( __( 'activated', 'translation-toolkit' ) == $data['status'] ) { echo 'active'; } else { echo 'inactive'; } // @todo ?>">
				<th class="check-column tt-item-icon">
					<img alt="" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/' . $data['img_type'] . '.gif'; ?>" />
					<div><strong><?php echo $data['type-desc']; ?></strong></div>
				</th>
				<td class="plugin-title">
					<strong><?php echo $data['name']; ?></strong> <span><?php echo __( 'by', 'translation-toolkit' ) . ' ' . $data['author']; ?></span>

					<div class="row-actions visible">
						<table border="0" width="100%">
							<tr>
								<td width="140px">
									<strong><?php _e( 'Textdomain', 'translation-toolkit' ); ?>:</strong>
								</td>
								<td>
									<?php echo $data['textdomain']['identifier']; ?><?php if ($data['textdomain']['is_const']) echo " (".__( 'defined by constant', 'translation-toolkit' ).")"; ?>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php _e( 'Version', 'translation-toolkit' ); ?>:</strong>
								</td>
								<td>
									<?php echo $data['version']; ?>
								</td>
							</tr>
							<tr>
								<td>
									<strong><?php _e( 'State', 'translation-toolkit' ); ?>:</strong>
								</td>
								<td>
									<?php echo $data['status']; ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<?php echo call_user_func( '__', $data['description'], $data['textdomain']['identifier'] ); ?>
								</td>
							</tr>
							<?php if ( isset( $data['dev-hints'] ) ) { ?>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td>
									<strong style="color: #f00;"><?php _e( 'Compatibility', 'translation-toolkit' ); ?>:</strong>
									<a align="left" class="question-help" href="javascript:void(0);" title="<?php _e( 'What does that mean?', 'translation-toolkit' ); ?>" rel="compatibility">
										<img src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/question.gif'; ?>" />
									</a>
								</td>
								<td>
									<?php echo $data['dev-hints']; ?>
								</td>
							</tr>
							<?php }
							if ( isset( $data['dev-security'] ) ) { ?>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td>
									<strong style="color: #f00;"><?php _e( 'Security Risk', 'translation-toolkit' ); ?>:</strong>
								</td>
								<td>
									<?php echo $data['dev-security']; ?>
								</td>
							</tr>
							<?php }
							if ( $data['type'] == 'wordpress-xxx' ) { ?>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td>
									<strong style="color: #f00;"><?php _e( 'Memory Warning', 'translation-toolkit' ); ?>:</strong>
								</td>
								<td>
									<?php _e( "Since WordPress 3.x version it may require at least <strong>58MB</strong> PHP memory_limit! The reason is still unclear but it doesn't freeze anymore. Instead a error message will be shown and the scanning process aborts while reaching your limits.", 'translation-toolkit' ); ?>
								</td>
							<tr>
							<?php }
							if ( $data['is-path-unclear'] ) { ?>
							<tr>
								<td>&nbsp;</td>
								<td>&nbsp;</td>
							</tr>
							<tr>
								<td><strong style="color: #f00;">
									<?php _e( 'Language Folder', 'translation-toolkit' ); ?>:</strong>
								</td>
								<td>
									<?php _e( 'The translation file folder is ambiguous, please select by clicking the appropriated language file folder or ask the Author about!', 'translation-toolkit' ); ?>
								</td>
							<tr>
							<?php } ?>
						</table>
					</div><!-- .row-actions .visible -->

					<?php
					if ( isset( $data['child-plugins'] ) ) {
						foreach( $data['child-plugins'] as $child ) { ?>
					<div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #ccc;">
						<h3 class="csp-type-name"><?php echo $child['name']; ?> <small><em><?php _e( 'by', 'translation-toolkit' ); ?> <?php echo $child['author']; ?></em></small></h3>
						<table border="0">
							<tr>
								<td><strong><?php _e( 'Version', 'translation-toolkit' ); ?>:</strong></td>
								<td width="100%"><?php echo $child['version']; ?></td>
							</tr>
							<tr>
								<td><strong><?php _e( 'State', 'translation-toolkit' ); ?>:</strong></td>
								<td class="csp-info-value csp-info-status"><?php echo $child['status']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><small><?php echo call_user_func( '__', $child['description'], $data['textdomain']['identifier']); ?></small></td>
							</tr>
						</table>
					</div>
				<?php } } ?>
				</td>
				<td class="component-details">
					<?php if ( $data['type'] == 'wordpress' && $data['is_US_Version'] ) { ?>
						<div style="color:#f00"><?php _e( "The original US version doesn't contain the language directory.", 'translation-toolkit' ); ?></div>
						<div>
							<a class="clickable button" onclick="csp_create_languange_path(this,'<?php echo str_replace( "\\", '/', WP_CONTENT_DIR ) . "/languages" ?>');">
								<?php _e( 'try to create the WordPress language directory', 'translation-toolkit' ); ?>
							</a>
						</div>
						<div>
							<?php _e( 'or create the missing directory using FTP Access as:', 'translation-toolkit' ); ?>
							<?php echo str_replace( "\\", '/', WP_CONTENT_DIR ) . "/"; ?><strong style="color:#f00;">languages</strong>
						</div>

					<?php } elseif ( $data['is-path-unclear'] ) { ?>
						<strong style="border-bottom: 1px solid #ccc;"><?php _e( 'Available Directories:', 'translation-toolkit' ); ?></strong>
						<ul>
						<?php
							$tmp = array();
							$dirs = TranslationToolkit_Helpers::rscanpath( $data['base_path'], $tmp );
							$dir = $data['base_path'];
							echo '<li><a class="clickable pot-folder" onclick="csp_create_pot_indicator(this,\''.$dir.$data['base_file'].'xx_XX.pot\')">' . str_replace( str_replace( "\\", "/", WP_PLUGIN_DIR ), '', $dir ) . '</a></li>';
							foreach( $dirs as $dir ) {
								echo '<li><a class="clickable pot-folder" onclick="csp_create_pot_indicator(this,\''.$dir.'/'.$data['base_file'].'xx_XX.pot\')">' . str_replace( str_replace( "\\", "/", WP_PLUGIN_DIR ), '', $dir ) . '</a></li>';
							}
						?>
						</ul>

					<?php } elseif ( $data['name'] == 'bbPress' && isset( $data['is_US_Version'] ) && $data['is_US_Version'] ) { ?>
						<div style="color:#f00;">
							<?php _e( "The original bbPress component doesn't contain a language directory.", 'translation-toolkit' ); ?>
						</div>
						<div>
							<a class="clickable button" onclick="csp_create_languange_path(this,'<?php echo $data['base_path'] . 'my-languages'; ?>');">
								<?php _e( 'try to create the bbPress language directory', 'translation-toolkit' ); ?>
							</a>
						</div>
						<div>
							<?php _e( 'or create the missing directory using FTP Access as:', 'translation-toolkit' ); ?>
							<?php echo $data['base_path']; ?><strong style="color:#f00;">my-languages</strong>
						</div>

					<?php } else { ?>
					<table width="100%" cellspacing="0" class="mo-list" id="mo-list-<?php echo ++$mo_list_counter; ?>" summary="<?php echo $data['textdomain']['identifier'].'|'.$data['type'].'|'.$data['name'].' v'.$data['version']; ?>">
						<thead>
							<tr class="mo-list-head">
								<td colspan="2" nowrap="nowrap">
									<img alt="GNU GetText" class="alignleft" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ); ?>images/gettext.gif" style="display:none;" />
									<a rel="<?php echo implode( '|', array_keys( $data['languages'] ) ); ?>" class="clickable mofile button" onclick="csp_add_language(this,'<?php echo $data['type']; ?>','<?php echo rawurlencode( $data['name'] ) . ' v' . $data['version'] . "','mo-list-" . $mo_list_counter . "','" . $data['base_path']."', '" . $data['base_file']."',this.rel,'" . $data['type']."','" . $data['simple-filename']."','" . $data['translation_template']."', '" . $data['textdomain']['identifier']."'," . ( $data['deny_scanning'] ? '1' : '0' ); ?>);">
										<?php _e( 'Add New Language', 'translation-toolkit' ); ?>
									</a>
									<?php
									if ( isset( $data['theme-self'] ) && ( $data['theme-self'] != $data['theme-template'] ) ) { ?>
										&nbsp;<a class="clickable mofile button" onclick="csp_merge_maintheme_languages(this,'<?php echo $data['theme-template']; ?>','<?php echo $data['theme-self']; ?>','<?php echo $data['base_path']; if(!empty($data['special_path'])) echo $data['special_path'].'/' ?>','<?php echo $data['textdomain']['identifier']; ?>','mo-list-<?php echo $mo_list_counter; ?>');"><?php _e( 'Sync Files with Main Theme', 'translation-toolkit' ); ?></a>
										<a rel="workonchildthemes" title="<?php _e( 'What does that mean?', 'translation-toolkit' ); ?>" href="javascript:void(0);" class="question-help" align="left">
											<img src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/question.gif'; ?>">
										</a>
									<?php
									} ?>
								</td>
								<td colspan="1" nowrap="nowrap" class="csp-ta-right">
									<?php echo sprintf( _n('<strong>%d</strong> Language', '<strong>%d</strong> Languages',count( $data['languages']), 'translation-toolkit' ), count( $data['languages'])); ?>
								</td>
							</tr>
							<tr class="mo-list-desc">
								<td nowrap="nowrap" align="left" class="lang-info-desc">
									<?php _e( 'Language', 'translation-toolkit' ); ?>
								</td>
								<td nowrap="nowrap" align="center">
									<?php _e( 'Permissions', 'translation-toolkit' ); ?></td>
								<td nowrap="nowrap" align="center">
									<?php _e( 'Actions', 'translation-toolkit' ); ?>
								</td>
							</tr>
						</thead>
						<tfoot>
							<tr class="mo-list-desc">
								<td nowrap="nowrap" align="left" class="lang-info-desc">
									<?php _e( 'Language', 'translation-toolkit' ); ?>
								</td>
								<td nowrap="nowrap" align="center">
									<?php _e( 'Permissions', 'translation-toolkit' ); ?></td>
								<td nowrap="nowrap" align="center">
									<?php _e( 'Actions', 'translation-toolkit' ); ?>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<?php
							foreach( $data['languages'] as $lang => $gtf ) {
								$country_www = isset( $sys_locales[ $lang ]) ? $sys_locales[ $lang ]['country-www'] : 'unknown';
								$lang_native = isset( $sys_locales[ $lang ]) ? $sys_locales[ $lang ]['lang-native'] : '<em>locale: </em>' . $lang;

								if ( $data['textdomain']['identifier'] == 'woocommerce' && $lang == 'de_DE') { // special case woocommerce german: start @todo remove? - David?
									$copy_base_file = $data['base_file']; $data['base_file'] = 'languages/informal/woocommerce-'; ?>
									<tr class="mo-file" lang="<?php echo $lang; ?>">
										<td nowrap="nowrap" width="100%" class="lang-info-desc"><img title="<?php _e( 'Locale', 'translation-toolkit' ); ?>: <?php echo $lang ?>" alt="(locale: <?php echo $lang; ?>)" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/flags/' . $country_www . '.gif'; ?>" /><?php if (get_locale() == $lang) echo "<strong>"; ?>&nbsp;<?php echo $lang_native.' '.__( '(informal)', 'translation-toolkit' ); ?><?php if (get_locale() == $lang) echo "</strong>"; ?></td>
										<td nowrap="nowrap" align="center">
											<div style="width:44px;">
												<?php if ( array_key_exists( 'po', $gtf)) {
													echo "<a class=\"csp-filetype-po".$gtf['po']['class']."\" title=\"".$gtf['po']['stamp'].($gtf['po']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".po".'\',\'csp-filetype-po-rw\');' : '')."\">&nbsp;</a>";
												} else { ?>
												<a class="csp-filetype-po" title="<?php _e( '-n.a.-', 'translation-toolkit' ); ?> [---|---|---]">&nbsp;</a>
												<?php } ?>
												<?php if ( array_key_exists( 'mo', $gtf)) {
													echo "<a class=\"csp-filetype-mo".$gtf['mo']['class']."\" title=\"".$gtf['mo']['stamp'].($gtf['mo']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".mo".'\',\'csp-filetype-mo-rw\' );' : '')."\">&nbsp;</a>";
												} else { ?>
												<a class="csp-filetype-mo" title="<?php _e( '-n.a.-', 'translation-toolkit' ); ?> [---|---|---]">&nbsp;</a>
												<?php } ?>
											</div>
										</td>
										<td nowrap="nowrap" style="padding-right: 5px;">
											<a class="clickable button" onclick="csp_launch_editor(this,'<?php echo $data['base_file'].$lang.".po" ; ?>','<?php echo $data['base_path']; ?>','<?php echo $data['textdomain']['identifier']; ?>');">
												<?php _e( 'Edit', 'translation-toolkit' ); ?>
											</a>
											<span>&nbsp;</span>
											<?php if ( !$data['deny_scanning'] ) { ?>
												<a class="clickable button" onclick="csp_rescan_language(this,'<?php echo rawurlencode( $data['name'] ) . ' v' . $data['version'] . "','mo-list-" . $mo_list_counter . "','" . $data['base_path'] . "','" . $data['base_file'] . "','" . $lang . "','" . $data['type'] . "','" .$data['simple-filename'] . "'"; ?>);">
													<?php _e( 'Rescan', 'translation-toolkit' ); ?>
												</a>
												<span>&nbsp;</span>
											<?php } else { ?>
											<span style="text-decoration: line-through;"><?php _e( 'Rescan', 'translation-toolkit' ); ?></span>
											<span>&nbsp;</span>
											<?php } ?>
											<a class="clickable button" onclick="csp_remove_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang; ?>');">
												<?php _e( 'Delete', 'translation-toolkit' ); ?>
											</a>
										</td>
									</tr>
									<?php $data['base_file'] = 'languages/formal/woocommerce-'; ?>
									<tr class="mo-file" lang="<?php echo $lang; ?>">
										<td nowrap="nowrap" width="100%" class="lang-info-desc"><img title="<?php _e( 'Locale', 'translation-toolkit' ); ?>: <?php echo $lang ?>" alt="(locale: <?php echo $lang; ?>)" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/flags/' . $country_www . '.gif'; ?>" /><?php if (get_locale() == $lang) echo "<strong>"; ?>&nbsp;<?php echo $lang_native.' '.__( '(formal)', 'translation-toolkit' ); ?><?php if (get_locale() == $lang) echo "</strong>"; ?></td>
										<td nowrap="nowrap" align="center">
											<div style="width:44px;">
												<?php if ( array_key_exists( 'po', $gtf)) {
													echo "<a class=\"csp-filetype-po".$gtf['po']['class']."\" title=\"".$gtf['po']['stamp'].($gtf['po']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".po".'\',\'csp-filetype-po-rw\' );' : '')."\">&nbsp;</a>";
												} else { ?>
												<a class="csp-filetype-po" title="<?php _e( '-n.a.-', 'translation-toolkit' ); ?> [---|---|---]">&nbsp;</a>
												<?php } ?>
												<?php if ( array_key_exists( 'mo', $gtf)) {
													echo "<a class=\"csp-filetype-mo".$gtf['mo']['class']."\" title=\"".$gtf['mo']['stamp'].($gtf['mo']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".mo".'\',\'csp-filetype-mo-rw\' );' : '')."\">&nbsp;</a>";
												} else { ?>
												<a class="csp-filetype-mo" title="<?php _e( '-n.a.-', 'translation-toolkit' ); ?> [---|---|---]">&nbsp;</a>
												<?php } ?>
											</div>
										</td>
										<td nowrap="nowrap" style="padding-right: 5px;">
											<a class="clickable button" onclick="csp_launch_editor(this,'<?php echo $data['base_file'] . $lang . '.po' ; ?>','<?php echo $data['base_path']; ?>','<?php echo $data['textdomain']['identifier']; ?>');">
												<?php _e( 'Edit', 'translation-toolkit' ); ?>
											</a>
											<span>&nbsp;</span>
											<?php if ( !$data['deny_scanning'] ) { ?>
											<a class="clickable button" onclick="csp_rescan_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."','".$data['type']."','".$data['simple-filename']."'"; ?>);">
												<?php _e( 'Rescan', 'translation-toolkit' ); ?>
											</a>
											<span>&nbsp;</span>
											<?php } else { ?>
											<span style="text-decoration: line-through;"><?php _e( 'Rescan', 'translation-toolkit' ); ?></span>
											<span>&nbsp;</span>
											<?php } ?>
											<a class="clickable button" onclick="csp_remove_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."'"; ?>);">
												<?php _e( 'Delete', 'translation-toolkit' ); ?>
											</a>
										</td>
									</tr>
									<?php $data['base_file'] =  $copy_base_file; ?>
									<tr class="mo-file" lang="<?php echo $lang; ?>">
										<td width="100%" colspan="3" class="lang-info-desc">
											<small>
												<strong style="color:#f00;"><?php _e( 'Warning', 'translation-toolkit' ); ?>: </strong>
												<?php _e( 'German translations are currently supported by a temporary workaround only, because they will be handled completely uncommon beside WordPress standards!', 'translation-toolkit' ); ?>
											</small>
										</td>
									</tr>
								<?php
								// special case woocommerce german: end
								} else { ?>
									<tr class="mo-file" lang="<?php echo $lang; ?>">
										<td nowrap="nowrap" width="100%" class="lang-info-desc">
											<img title="<?php _e( 'Locale', 'translation-toolkit' ); ?>: <?php echo $lang ?>" alt="(locale: <?php echo $lang; ?>)" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/flags/' . $country_www . '.gif'; ?>" />
											<?php if ( get_locale() == $lang ) echo "<strong>"; ?>&nbsp;<?php echo $lang_native; ?><?php if (get_locale() == $lang) echo "</strong>"; ?>
										</td>
										<td nowrap="nowrap" align="center">
											<div style="width:44px">
												<?php if ( array_key_exists( 'po', $gtf ) ) {
													echo "<a class=\"csp-filetype-po".$gtf['po']['class']."\" title=\"".$gtf['po']['stamp'].($gtf['po']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".po".'\',\'csp-filetype-po-rw\' );' : '')."\">&nbsp;</a>";
												} else { ?>
												<a class="csp-filetype-po" title="<?php _e( '-n.a.-', 'translation-toolkit' ); ?> [---|---|---]">&nbsp;</a>
												<?php } ?>
												<?php
												if ( array_key_exists( 'mo', $gtf ) ) {
													echo "<a class=\"csp-filetype-mo".$gtf['mo']['class']."\" title=\"".$gtf['mo']['stamp'].($gtf['mo']['class'] == '-r' ? '" onclick="csp_make_writable(this,\''.$data['base_path'].$data['base_file'].$lang.".mo".'\',\'csp-filetype-mo-rw\' );' : '')."\">&nbsp;</a>";
												} else { ?>
													<a class="csp-filetype-mo" title="<?php _e( '-n.a.-', 'translation-toolkit' ); ?> [---|---|---]">&nbsp;</a>
												<?php } ?>
											</div>
										</td>
										<td nowrap="nowrap">
											<a class="clickable button" onclick="csp_launch_editor(this, '<?php echo $data['base_file'].$lang.".po" ; ?>', '<?php echo $data['base_path']; ?>','<?php echo $data['textdomain']['identifier']; ?>' )">
												<?php _e( 'Edit', 'translation-toolkit' ); ?>
											</a>
											<?php
											if ( !$data['deny_scanning'] ) {
												if ( isset( $data['theme-self'] ) && ( $data['theme-self'] != $data['theme-template'] ) ) { ?>
													<a class="clickable button" onclick="csp_rescan_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."','".$data['type']."','".$data['simple-filename']."','".$data['theme-template']."'"; ?>);">
														<?php _e( 'Rescan', 'translation-toolkit' ); ?>
													</a>
													<?php } else { ?>
													<a class="clickable button" onclick="csp_rescan_language(this,'<?php echo rawurlencode($data['name'])." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang."','".$data['type']."','".$data['simple-filename']."',''"; ?>);">
														<?php _e( 'Rescan', 'translation-toolkit' ); ?>
													</a>
													<?php }
											} else { ?>
												<span style="text-decoration: line-through;"><?php _e( 'Rescan', 'translation-toolkit' ); ?></span>
											<?php } ?>
											<a class="clickable button" onclick="csp_remove_language(this,'<?php echo rawurlencode( $data['name'] )." v".$data['version']."','mo-list-".$mo_list_counter."','".$data['base_path']."','".$data['base_file']."','".$lang; ?>');">
												<?php _e( 'Delete', 'translation-toolkit' ); ?>
											</a>
										</td>
									</tr>
								<?php } ?>
							<?php } ?>
						</tbody>
					</table>
					<?php } ?>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div><!-- csp-wrap-main closed -->


<div id="csp-wrap-editor" class="wrap" style="display:none;">
	<h2>
		<?php _e( 'Translate Language File', 'translation-toolkit' ); ?>
		<a class="add-new-h2" href="#" onclick="window.location.reload()"><?php _e( 'back to overview page &raquo;', 'translation-toolkit' ); ?></a>
	</h2>
	<div id="csp-json-header">
		<div class="po-header-toggle">
			<span><b><?php _e( 'Project-Id-Version:', 'translation-toolkit' ); ?></b></span> <span id="prj-id-ver">---</span> | <strong><?php _e( 'File:', 'translation-toolkit' ); ?></strong> <a onclick="csp_toggle_header(this,'po-hdr');"><?php _e( 'unknown', 'translation-toolkit' ); ?></a></div>
	</div>
	<div class="action-bar">
		<p id="textdomain-error" class="hidden"><small><?php
			_e( '<strong>Error</strong>: The actual loaded translation content does not match the textdomain:', 'translation-toolkit' );
			echo '&nbsp;<span></span><br/>';
			_e( 'Expect, that any text you translate will not occure as long as the textdomain is mismatching!', 'translation-toolkit' );
			echo '<br/>';
			_e( 'This is a coding issue at the source files you try to translate, please contact the original Author and explain this mismatch.', 'translation-toolkit' );
		?>&nbsp;<a class="question-help" href="javascript:void(0);" title="<?php _e( 'What does that mean?', 'translation-toolkit' ); ?>" rel="textdomain"><img src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/question.gif'; ?>" /></a></small></p>
		<p id="textdomain-warning" class="hidden"><small><?php
			_e( '<strong>Warning</strong>: The actual loaded translation content contains mixed textdomains and is not pure translateable within one textdomain.', 'translation-toolkit' );
			echo '<br/>';
			_e( 'It seems, that there is code contained extracted out of other plugins, themes or widgets and used by copy & paste inside some source files.', 'translation-toolkit' );
			echo '<br/>';
			_e( 'The affected unknown textdomains are:', 'translation-toolkit' );
			echo '&nbsp;<span>&nbsp;</span>';
		?>&nbsp;<a class="question-help" href="javascript:void(0);" title="<?php _e( 'What does that mean?', 'translation-toolkit' ); ?>" rel="textdomain"><img src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/question.gif'; ?>" /></a></small></p>
		<div class="alignleft" id="csp-mo-textdomain">
			<b><?php _e( 'Textdomain:', 'translation-toolkit' ); ?></b>
			<a class="question-help" href="javascript:void(0);" title="<?php _e( 'What does that mean?', 'translation-toolkit' ); ?>" rel="textdomain">
				<img src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/question.gif'; ?>" />
			</a>
			<select id="csp-mo-textdomain-val" onchange="csp_change_textdomain_view(this.value);"></select>
		</div>
		<div class="alignleft">
			<input id="csp-write-mo-file" class="button button-secondary" style="display:none;" type="submit" value="<?php _e( 'generate mo-file', 'translation-toolkit' ); ?>" onclick="csp_generate_mofile(this);" />
		</div>
		<div class="alignleft">
			<?php _e( 'last written:', 'translation-toolkit' ); ?>
			<span id="catalog-last-saved" ><?php _e( 'unknown', 'translation-toolkit' ); ?></span>
			<img id="csp-generate-mofile" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/'; ?>write-mofile.gif" />
		</div>
		<br class="clear" />
	</div>
	<ul class="subsubsub">
		<li><a id="csp-filter-all" class="csp-filter current" onclick="csp_filter_result(this, csp_idx.total);"><?php _e( 'Total', 'translation-toolkit' ); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-plurals" class="csp-filter" onclick="csp_filter_result(this, csp_idx.plurals);"><?php _e( 'Plural', 'translation-toolkit' ); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-ctx" class="csp-filter" onclick="csp_filter_result(this, csp_idx.ctx);"><?php _e( 'Context', 'translation-toolkit' ); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-open" class="csp-filter" onclick="csp_filter_result(this, csp_idx.open);"><?php _e( 'Not translated', 'translation-toolkit' ); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-rem" class="csp-filter" onclick="csp_filter_result(this, csp_idx.rem);"><?php _e( 'Comments', 'translation-toolkit' ); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-code" class="csp-filter" onclick="csp_filter_result(this, csp_idx.code);"><?php _e( 'Code Hint', 'translation-toolkit' ); ?> ( <span class="csp-flt-cnt">0</span> )</a> | </li>
		<li><a id="csp-filter-trail" class="csp-filter" onclick="csp_filter_result(this, csp_idx.trail);"><?php _e( 'Trailing Space', 'translation-toolkit' ); ?> ( <span class="csp-flt-cnt">0</span> )</a></li>
		<li style="display:none;"> | <span id="csp-filter-search" class="current"><?php _e( 'Search Result', 'translation-toolkit' ); ?>  ( <span class="csp-flt-cnt">0</span> )</span></li>
		<li style="display:none;"> | <span id="csp-filter-regexp" class="current"><?php _e( 'Expression Result', 'translation-toolkit' ); ?>  ( <span class="csp-flt-cnt">0</span> )</span></li>
	</ul>

	<div class="tablenav top">
		<div class="alignleft actions">
			<div class="alignleft" style="padding-top: 5px;font-size:11px;"><strong><?php _e( 'Page Size', 'translation-toolkit' ); ?>:&nbsp;</strong></div>
			<select id="catalog-pagesize" name="catalog-pagesize" onchange="csp_change_pagesize(this.value);" class="alignleft" style="font-size:11px;" autocomplete="off">
				<option value="10">10</option>
				<option value="25">25</option>
				<option value="50">50</option>
				<option value="75">75</option>
				<option value="100" selected="selected">100</option>
				<option value="150">150</option>
				<option value="200">200</option>
			</select>
		</div>
		<div class="tablenav-pages">
			<span class="displaying-num">X items</span>
			<span class="pagination-links">
				<a href="#" title="<?php _e( 'Go to the first page', 'translation-toolkit' ); ?>" class="first-page"><?php _e( '&laquo;', 'translation-toolkit' ); ?></a>
				<a href="#" title="<?php _e( 'Go to the previous page', 'translation-toolkit' ); ?>" class="prev-page">‹</a>
				<span class="paging-input">
					<input class="current-page" type="text" size="1" value="1" name="paged" title="Current page">
					of
					<span class="total-pages">2</span>
				</span>
				<a href="#" title="<?php _e( 'Go to the next page', 'translation-toolkit' ); ?>" class="next-page">›</a>
				<a href="#" title="<?php _e( 'Go to the last page', 'translation-toolkit' ); ?>" class="last-page"><?php _e( '&raquo;', 'translation-toolkit' ); ?></a>
			</span>
		</div>
		<br class="clear">
	</div>

	<table class="widefat" cellspacing="0">
		<thead>
			<tr>
				<th nowrap="nowrap">
					<span><?php _e( 'Infos', 'translation-toolkit' ); ?></span>
				</th>
				<th width="45%">
					<?php _e( 'Original:', 'translation-toolkit' ); ?>
					<input id="s_original" name="s_original" type="text" size="16" value="" onkeyup="csp_search_result(this);" style="margin-bottom:3px;" autocomplete="off" />
					<input id="ignorecase_key" name="ignorecase_key" type="checkbox" value="" onclick="csp_search_key('s_original');" />
					<label for="ignorecase_key" style="font-weight:normal;margin-top:-2px;"> <?php _e( 'non case-sensitive', 'translation-toolkit' ) ?></label>
					<a class="clickable regexp" onclick="csp_search_regexp('s_original');"></a>
				</th>
				<th width="45%">
					<?php _e( 'Translation:', 'translation-toolkit' ); ?>
					<input id="s_translation" name="s_translation" type="text" size="16" value="" onkeyup="csp_search_result(this);" style="margin-bottom:3px;" autocomplete="off" />
					<input id="ignorecase_val" name="ignorecase_val" type="checkbox" value="" onclick="csp_search_val('s_translation');" />
					<label for="ignorecase_val" style="font-weight:normal;margin-top:-2px;"> <?php _e( 'non case-sensitive', 'translation-toolkit' ); ?></label>
					<a class="clickable regexp" onclick="csp_search_regexp('s_translation');"></a>
				</th>
				<th nowrap="nowrap">
					<span><?php _e( 'Actions', 'translation-toolkit' ); ?></span>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th nowrap="nowrap">
					<span><?php _e( 'Infos', 'translation-toolkit' ); ?></span>
				</th>
				<th width="45%">
					<?php _e( 'Original:', 'translation-toolkit' ); ?>
					<input id="s_original" name="s_original" type="text" size="16" value="" onkeyup="csp_search_result(this;)" style="margin-bottom:3px;" autocomplete="off" />
					<input id="ignorecase_key" name="ignorecase_key" type="checkbox" value="" onclick="csp_search_key('s_original');" />
					<label for="ignorecase_key" style="font-weight:normal;margin-top:-2px;"> <?php _e( 'non case-sensitive', 'translation-toolkit' ); ?></label>
					<a class="clickable regexp" onclick="csp_search_regexp('s_original')"></a>
				</th>
				<th width="45%">
					<?php _e( 'Translation:', 'translation-toolkit' ); ?>
					<input id="s_translation" name="s_translation" type="text" size="16" value="" onkeyup="csp_search_result(this)" style="margin-bottom:3px;" autocomplete="off" />
					<input id="ignorecase_val" name="ignorecase_val" type="checkbox" value="" onclick="csp_search_val('s_translation');" />
					<label for="ignorecase_val" style="font-weight:normal;margin-top:-2px;"> <?php _e( 'non case-sensitive', 'translation-toolkit' ); ?></label>
					<a class="clickable regexp" onclick="csp_search_regexp('s_translation');"></a>
				</th>
				<th nowrap="nowrap">
					<span><?php _e( 'Actions', 'translation-toolkit' ); ?></span>
				</th>
			</tr>
		</tfoot>
		<tbody id="catalog-body">
			<tr>
				<td colspan="4" align="center">
					<img alt="" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/loading.gif'?>" />
					<br />
					<span style="color:#328AB2;"><?php _e( 'Please wait, file content presently being loaded ...', 'translation-toolkit' ); ?></span>
				</td>
			</tr>
		</tbody>
	</table>


	<div class="tablenav bottom">
		<div class="alignleft actions">
			<a class="alignleft button" href="javascript:void(0);" onclick="window.scrollTo(0,0);"><?php _e( 'scroll to top', 'translation-toolkit' ); ?></a>
		</div>
		<div class="tablenav-pages">
			<span class="displaying-num">X items</span>
			<span class="pagination-links">
				<a href="#" title="<?php _e( 'Go to the first page', 'translation-toolkit' ); ?>" class="first-page"><?php _e( '&laquo;', 'translation-toolkit' ); ?></a>
				<a href="#" title="<?php _e( 'Go to the previous page', 'translation-toolkit' ); ?>" class="prev-page">‹</a>
				<span class="paging-input">
					1 of
					<span class="total-pages">2</span>
				</span>
				<a href="#" title="<?php _e( 'Go to the next page', 'translation-toolkit' ); ?>" class="next-page">›</a>
				<a href="#" title="<?php _e( 'Go to the last page', 'translation-toolkit' ); ?>" class="last-page"><?php _e( '&raquo;', 'translation-toolkit' ); ?></a>
			</span>
		</div>
		<br class="clear">
	</div>

	<div class="tablenav">
		<div id="catalog-pages-bottom" class="tablenav-pages">
			<a href="#" class="prev page-numbers"><?php _e( '&laquo;', 'translation-toolkit' ); ?></a>
			<a href="#" class="page-numbers">1</a>
			<a href="#" class="page-numbers">2</a>
			<a href="#" class="page-numbers">3</a>
			<span class="page-numbers current">4</span>
			<a href="#" class="next page-numbers"><?php _e( '&raquo;', 'translation-toolkit' ); ?></a>
		</div>
		<br class="clear" />
	</div>

	<br class="clear" />
</div><!-- csp-wrap-editor closed -->


<div id="csp-dialog-container" style="display:none;">
	<div>
		<h3 id="csp-dialog-header">
			<img alt="" id="csp-dialog-icon" class="alignleft" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ); ?>images/gettext.gif" />
			<span id="csp-dialog-caption" class="alignleft"><?php _e( 'Edit Catalog Entry', 'translation-toolkit' ); ?></span>
			<img alt="" id="csp-dialog-cancel" class="alignright clickable" title="<?php _e( 'close', 'translation-toolkit' ); ?>" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/close.gif'; ?>" onclick="csp_cancel_dialog();" />
			<br class="clear" />
		</h3>
		<div id="csp-dialog-body"></div>
		<div style="text-align:center;">
			<img id="csp-dialog-saving" src="<?php echo plugin_dir_url( TranslationToolkit::get_file() ); ?>images/saving.gif" style="margin-top:20%;display:none;" />
		</div>
	</div>
</div><!-- csp-dialog-container closed -->


<div id="csp-credentials"></div><!-- credential for filesystem -->
<br />
