<?php
/**
 * Settings layout fields.
 *
 * @package ProjectPress
 * @subpackage Admin\Manager\Settings
 * @since 1.0.0
 */
namespace ProjectPress\Admin\Manager\Settings;

use ProjectPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsLayout {
	/**
	 * Render layout settings fields.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @param string $section Section slug.
	 * @return void
	 */
	public function render( array $values, string $section = 'general' ): void {
		$section = in_array( $section, [ 'general', 'search', 'sidebar', 'page' ], true ) ? $section : 'general';
		$can_edit = current_user_can( 'manage_options' ) || current_user_can( 'projectpress_settings_layout_edit' );
		?>
		<?php if ( $can_edit ) : ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="projectpress-settings-form">
			<input type="hidden" name="action" value="projectpress_save_layout_settings" />
			<input type="hidden" name="projectpress_tab" value="layout" />
			<input type="hidden" name="projectpress_layout[layout_section]" value="<?php echo esc_attr( $section ); ?>" />
			<?php wp_nonce_field( 'projectpress_save_layout_settings' ); ?>
		<?php endif; ?>
			<nav aria-label="<?php echo esc_attr__( 'Layout settings sections', 'projectpress' ); ?>" class="mb-3">
				<div class="nav nav-tabs" id="projectpress-layout-tab" role="tablist">
					<a 
						href="<?php echo esc_url( admin_url( 'admin.php?page=projectpress&group=settings&tab=layout&s=general' ) ); ?>" 
						class="nav-link <?php echo 'general' === $section ? 'active' : ''; ?>" 
						id="projectpress-layout-general-tab" 
						role="tab" 
						aria-controls="projectpress-layout-general" 
						aria-selected="<?php echo 'general' === $section ? 'true' : 'false'; ?>">
						<?php esc_html_e( 'General', 'projectpress' ); ?>
					</a>
					<a 
						href="<?php echo esc_url( admin_url( 'admin.php?page=projectpress&group=settings&tab=layout&s=search' ) ); ?>" 
						class="nav-link <?php echo 'search' === $section ? 'active' : ''; ?>" 
						id="projectpress-layout-search-tab" 
						role="tab" 
						aria-controls="projectpress-layout-search" 
						aria-selected="<?php echo 'search' === $section ? 'true' : 'false'; ?>">
						<?php esc_html_e( 'Mod Search', 'projectpress' ); ?>
					</a>
					<a 
						href="<?php echo esc_url( admin_url( 'admin.php?page=projectpress&group=settings&tab=layout&s=sidebar' ) ); ?>" 
						class="nav-link <?php echo 'sidebar' === $section ? 'active' : ''; ?>" 
						id="projectpress-layout-sidebar-tab" 
						role="tab" 
						aria-controls="projectpress-layout-sidebar" 
						aria-selected="<?php echo 'sidebar' === $section ? 'true' : 'false'; ?>">
						<?php esc_html_e( 'Mod Sidebar', 'projectpress' ); ?>
					</a>
					<a 
						href="<?php echo esc_url( admin_url( 'admin.php?page=projectpress&group=settings&tab=layout&s=page' ) ); ?>" 
						class="nav-link <?php echo 'page' === $section ? 'active' : ''; ?>" 
						id="projectpress-layout-page-tab" 
						role="tab" 
						aria-controls="projectpress-layout-page" 
						aria-selected="<?php echo 'page' === $section ? 'true' : 'false'; ?>">
						<?php esc_html_e( 'Mod Page', 'projectpress' ); ?>
					</a>
				</div>
			</nav>
			<div class="tab-content" id="projectpress-layout-tab-content">
				<?php if ( 'general' === $section ) : ?>
					<?php $this->section_general( $values ); ?>
				<?php elseif ( 'search' === $section ) : ?>
					<?php $this->section_search( $values ); ?>
				<?php elseif ( 'sidebar' === $section ) : ?>
					<?php $this->section_sidebar( $values ); ?>
				<?php else : ?>
					<?php $this->section_page( $values ); ?>
				<?php endif; ?>
			</div>
		<?php if ( $can_edit ) : ?>
			<div class="mt-3 d-flex justify-content-end">
				<?php echo FormFieldHelper::button( 
					__( 'Save layout settings', 'projectpress' ), 
					[ 
						'type' => 'submit', 
						'class' => 'btn btn-primary' 
					] 
				); ?>
			</div>
		</form>
		<?php endif; ?>
		<?php
	}

	/**
	 * Render the general section.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	private function section_general( array $values ): void {
		?>
		<div class="tab-pane fade show active" id="projectpress-layout-general" role="tabpanel" aria-labelledby="projectpress-layout-general-tab" tabindex="0">
			<table class="form-table table align-middle" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-show-search', 
								__( 'Enable Mod Search', 'projectpress' ), 
								[ 
									'description' => __( 'Display the Mod search interface.', 'projectpress' ), 
									'tooltip' => __( 'Search is one of the fastest ways to find content in a large Mod.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[show_search]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-show-search', 
									'checked' => ! empty( $values['show_search'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-show-breadcrumbs', 
								__( 'Show Breadcrumbs', 'projectpress' ), 
								[ 
									'description' => __( 'Display the current Mod hierarchy.', 'projectpress' ), 
									'tooltip' => __( 'Breadcrumbs connect the current page to its parent structure.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[show_breadcrumbs]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-show-breadcrumbs', 
									'checked' => ! empty( $values['show_breadcrumbs'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-show-sidebar', 
								__( 'Show Mod Sidebar', 'projectpress' ), 
								[ 
									'description' => __( 'Display the Mod navigation sidebar.', 'projectpress' ), 
									'tooltip' => __( 'The sidebar keeps parent and child Mod pages discoverable.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[show_sidebar]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-show-sidebar', 
									'checked' => ! empty( $values['show_sidebar'] ) 
								] 
							); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render the search section.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	private function section_search( array $values ): void {
		?>
		<div class="tab-pane fade show active" id="projectpress-layout-search" role="tabpanel" aria-labelledby="projectpress-layout-search-tab" tabindex="0">
			<table class="form-table table align-middle" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-search-placeholder', 
								__( 'Search Placeholder', 'projectpress' ), 
								[ 
									'description' => __( 'Text shown before a visitor enters a search term.', 'projectpress' ), 
									'tooltip' => __( 'Keep this short so it fits comfortably in the search field.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::input( 
								'projectpress_layout[search_placeholder]', 
								isset( $values['search_placeholder'] ) ? (string) $values['search_placeholder'] : 'Search the Mod', 
								[ 
									'id' => 'projectpress-layout-search-placeholder', 
									'type' => 'text', 
									'class' => 'w-100' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-search-button-text', 
								__( 'Search Button Text', 'projectpress' ), 
								[ 
									'description' => __( 'Text shown on the search submit button.', 'projectpress' ), 
									'tooltip' => __( 'Use a clear action such as Search.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::input( 
								'projectpress_layout[search_button_text]', 
								isset( $values['search_button_text'] ) ? (string) $values['search_button_text'] : 'Search', 
								[ 
									'id' => 'projectpress-layout-search-button-text', 
									'type' => 'text', 
									'class' => 'w-100' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-search-scope', 
								__( 'Search Scope', 'projectpress' ), 
								[ 
									'description' => __( 'Choose which parts of Mod pages are searched.', 'projectpress' ), 
									'tooltip' => __( 'Searching titles and content gives visitors the broadest results.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::select( 
								'projectpress_layout[search_scope]', 
								[ 
									'all' => __( 'Titles and Content', 'projectpress' ), 
									'title' => __( 'Titles Only', 'projectpress' ), 
									'content' => __( 'Content Only', 'projectpress' ) 
								], 
								isset( $values['search_scope'] ) ? (string) $values['search_scope'] : 'all', 
								[ 
									'id' => 'projectpress-layout-search-scope' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-search-no-results-message', 
								__( 'No Results Message', 'projectpress' ), 
								[ 
									'description' => __( 'Message shown when a Mod search returns no results.', 'projectpress' ), 
									'tooltip' => __( 'Give visitors a useful next step instead of leaving the result area empty.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::input( 
								'projectpress_layout[search_no_results_message]', 
								isset( $values['search_no_results_message'] ) ? (string) $values['search_no_results_message'] : 'No Mod pages found.', 
								[ 
									'id' => 'projectpress-layout-search-no-results-message', 
									'type' => 'text', 
									'class' => 'w-100' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-search-results-count', 
								__( 'Search Results Per Page', 'projectpress' ), 
								[ 
									'description' => __( 'Maximum number of results shown for one search.', 'projectpress' ), 
									'tooltip' => __( 'Use a smaller value for compact result lists.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::input( 
								'projectpress_layout[search_results_count]', 
								isset( $values['search_results_count'] ) ? (string) $values['search_results_count'] : '10', 
								[ 
									'id' => 'projectpress-layout-search-results-count', 
									'type' => 'number', 
									'min' => 1, 
									'max' => 50, 
									'class' => 'w-100' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-search-min-chars', 
								__( 'Minimum Search Characters', 'projectpress' ), 
								[ 
									'description' => __( 'Minimum number of characters required before a search is performed.', 'projectpress' ), 
									'tooltip' => __( 'A small minimum prevents noisy searches from very short terms.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::input( 
								'projectpress_layout[search_min_chars]', 
								isset( $values['search_min_chars'] ) ? (string) $values['search_min_chars'] : '2', 
								[ 
									'id' => 'projectpress-layout-search-min-chars', 
									'type' => 'number', 
									'min' => 1, 
									'max' => 5, 
									'class' => 'w-100' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-search-live-results', 
								__( 'Live Search Results', 'projectpress' ), 
								[ 
									'description' => __( 'Update suggestions while visitors type.', 'projectpress' ), 
									'tooltip' => __( 'Live results make large Mods easier to browse.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[search_live_results]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-search-live-results', 
									'checked' => ! empty( $values['search_live_results'] ) 
								] 
							); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render the sidebar section.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	private function section_sidebar( array $values ): void {
		?>
		<div class="tab-pane fade show active" id="projectpress-layout-sidebar" role="tabpanel" aria-labelledby="projectpress-layout-sidebar-tab" tabindex="0">
			<table class="form-table table align-middle" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-sidebar-show-sidebar', 
								__( 'Show Mod Sidebar', 'projectpress' ), 
								[ 
									'description' => __( 'Display the Mod navigation sidebar on Mod pages.', 'projectpress' ), 
									'tooltip' => __( 'The sidebar is the primary navigation structure for ProjectPress.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[show_sidebar]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-sidebar-show-sidebar', 
									'checked' => ! empty( $values['show_sidebar'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-sidebar-position', 
								__( 'Sidebar Position', 'projectpress' ), 
								[ 
									'description' => __( 'Choose which side of the page contains the Mod navigation.', 'projectpress' ), 
									'tooltip' => __( 'Use the position that best matches your site layout.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::select( 
								'projectpress_layout[sidebar_position]', 
								[ 
									'left' => __( 'Left', 'projectpress' ), 
									'right' => __( 'Right', 'projectpress' ) 
								], 
								isset( $values['sidebar_position'] ) ? (string) $values['sidebar_position'] : 'left', 
								[ 
									'id' => 'projectpress-layout-sidebar-position' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-sidebar-width', 
								__( 'Sidebar Width', 'projectpress' ), 
								[ 
									'description' => __( 'Set the preferred width of the Mod navigation sidebar in pixels.', 'projectpress' ), 
									'tooltip' => __( 'Allow enough room for long category names without crowding the content.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::input( 
								'projectpress_layout[sidebar_width]', 
								isset( $values['sidebar_width'] ) ? (string) $values['sidebar_width'] : '280', 
								[ 
									'id' => 'projectpress-layout-sidebar-width', 
									'type' => 'number', 
									'min' => 180, 
									'max' => 480, 
									'class' => 'w-100' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-sidebar-sticky', 
								__( 'Sticky Sidebar', 'projectpress' ), 
								[ 
									'description' => __( 'Keep the Mod navigation visible while the page scrolls.', 'projectpress' ), 
									'tooltip' => __( 'Sticky navigation is useful for long Mod pages.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[sidebar_sticky]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-sidebar-sticky', 
									'checked' => ! empty( $values['sidebar_sticky'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-sidebar-show-categories', 
								__( 'Show Categories', 'projectpress' ), 
								[ 
									'description' => __( 'Display Mod categories in the navigation.', 'projectpress' ), 
									'tooltip' => __( 'Disable this when navigation is provided by a custom component.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[sidebar_show_categories]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-sidebar-show-categories', 
									'checked' => ! empty( $values['sidebar_show_categories'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-sidebar-show-category-count', 
								__( 'Show Category Counts', 'projectpress' ), 
								[ 
									'description' => __( 'Display the number of pages in each category.', 'projectpress' ), 
									'tooltip' => __( 'Counts help visitors understand the size of each section.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[sidebar_show_category_count]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-sidebar-show-category-count', 
									'checked' => ! empty( $values['sidebar_show_category_count'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-sidebar-expand-categories', 
								__( 'Expand Categories', 'projectpress' ), 
								[ 
									'description' => __( 'Show child categories when the sidebar loads.', 'projectpress' ), 
									'tooltip' => __( 'Disable this for Mods with many nested categories.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[sidebar_expand_categories]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-sidebar-expand-categories', 
									'checked' => ! empty( $values['sidebar_expand_categories'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-sidebar-show-page-count', 
								__( 'Show Page Counts', 'projectpress' ), 
								[ 
									'description' => __( 'Display page counts beside Mod navigation items.', 'projectpress' ), 
									'tooltip' => __( 'Page counts provide a quick overview of navigation depth.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[sidebar_show_page_count]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-sidebar-show-page-count', 
									'checked' => ! empty( $values['sidebar_show_page_count'] ) 
								] 
							); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Render the page section.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	private function section_page( array $values ): void {
		?>
		<div class="tab-pane fade show active" id="projectpress-layout-page" role="tabpanel" aria-labelledby="projectpress-layout-page-tab" tabindex="0">
			<table class="form-table table align-middle" role="presentation">
				<tbody>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-show-title', 
								__( 'Show Page Title', 'projectpress' ), 
								[ 
									'description' => __( 'Display the Mod page title above its content.', 'projectpress' ), 
									'tooltip' => __( 'Disable this when your page template already renders the title.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[page_show_title]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-page-show-title', 
									'checked' => ! empty( $values['page_show_title'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-show-breadcrumbs', 
								__( 'Show Breadcrumbs', 'projectpress' ), 
								[ 
									'description' => __( 'Show the Mod hierarchy above the page content.', 'projectpress' ), 
									'tooltip' => __( 'Breadcrumbs help visitors understand where the current page belongs.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[show_breadcrumbs]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-page-show-breadcrumbs', 
									'checked' => ! empty( $values['show_breadcrumbs'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-show-toc', 
								__( 'Show Table of Contents', 'projectpress' ), 
								[ 
									'description' => __( 'Display a table of contents generated from page headings.', 'projectpress' ), 
									'tooltip' => __( 'The table of contents helps visitors scan long pages.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[page_show_toc]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-page-show-toc', 
									'checked' => ! empty( $values['page_show_toc'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-toc-position', 
								__( 'Table of Contents Position', 'projectpress' ), 
								[ 
									'description' => __( 'Choose where the page table of contents appears.', 'projectpress' ), 
									'tooltip' => __( 'Place it in the sidebar to keep navigation together or in the content area for a more compact layout.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::select( 
								'projectpress_layout[page_toc_position]', 
								[ 
									'sidebar' => __( 'Mod Sidebar', 'projectpress' ), 
									'content' => __( 'Above Page Content', 'projectpress' ) 
								], 
								isset( $values['page_toc_position'] ) ? (string) $values['page_toc_position'] : 'sidebar', 
								[ 
									'id' => 'projectpress-layout-page-toc-position' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-toc-min-level', 
								__( 'TOC Minimum Heading', 'projectpress' ), 
								[ 
									'description' => __( 'The shallowest heading included in the table of contents.', 'projectpress' ), 
									'tooltip' => __( 'Heading 2 is a useful default for most documentation pages.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::input( 
								'projectpress_layout[toc_min_level]', 
								isset( $values['toc_min_level'] ) ? (string) $values['toc_min_level'] : '2', 
								[ 
									'id' => 'projectpress-layout-page-toc-min-level', 
									'type' => 'number', 
									'min' => 1, 
									'max' => 5, 
									'class' => 'w-100' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-toc-max-level', 
								__( 'TOC Maximum Heading', 'projectpress' ), 
								[ 
									'description' => __( 'The deepest heading included in the table of contents.', 'projectpress' ), 
									'tooltip' => __( 'Avoid including every heading level on very detailed pages.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::input( 
								'projectpress_layout[toc_max_level]', 
								isset( $values['toc_max_level'] ) ? (string) $values['toc_max_level'] : '4', 
								[ 
									'id' => 'projectpress-layout-page-toc-max-level', 
									'type' => 'number', 
									'min' => 2, 
									'max' => 6, 
									'class' => 'w-100' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-show-last-updated', 
								__( 'Show Last Updated Date', 'projectpress' ), 
								[ 
									'description' => __( 'Display when the Mod page was last updated.', 'projectpress' ), 
									'tooltip' => __( 'This reassures visitors that the page is maintained.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[show_last_updated]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-page-show-last-updated', 
									'checked' => ! empty( $values['show_last_updated'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-show-author', 
								__( 'Show Page Author', 'projectpress' ), 
								[ 
									'description' => __( 'Display the author of the Mod page.', 'projectpress' ), 
									'tooltip' => __( 'Useful when Mods are maintained by multiple contributors.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[show_author]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-page-show-author', 
									'checked' => ! empty( $values['show_author'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-show-reading-time', 
								__( 'Show Reading Time', 'projectpress' ), 
								[ 
									'description' => __( 'Display an estimated reading time for the page.', 'projectpress' ), 
									'tooltip' => __( 'Reading time helps visitors decide how much time to set aside.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[show_reading_time]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-page-show-reading-time', 
									'checked' => ! empty( $values['show_reading_time'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-reading-speed', 
								__( 'Reading Speed', 'projectpress' ), 
								[ 
									'description' => __( 'Words per minute used to calculate reading time.', 'projectpress' ), 
									'tooltip' => __( 'Use a lower value when your audience benefits from a slower estimate.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::input( 
								'projectpress_layout[reading_time_wpm]', 
								isset( $values['reading_time_wpm'] ) ? (string) $values['reading_time_wpm'] : '200', 
								[ 
									'id' => 'projectpress-layout-page-reading-speed', 
									'type' => 'number', 
									'min' => 100, 
									'max' => 400, 
									'class' => 'w-100' 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-show-feedback', 
								__( 'Show Page Feedback', 'projectpress' ), 
								[ 
									'description' => __( 'Ask visitors whether the Mod page was helpful.', 'projectpress' ), 
									'tooltip' => __( 'Feedback gives Mod maintainers a simple quality signal.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[show_feedback]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-page-show-feedback', 
									'checked' => ! empty( $values['show_feedback'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-show-navigation', 
								__( 'Show Previous and Next Links', 'projectpress' ), 
								[ 
									'description' => __( 'Add navigation links between related Mod pages.', 'projectpress' ), 
									'tooltip' => __( 'This gives visitors another way to move through a Mod.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[page_show_navigation]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-page-show-navigation', 
									'checked' => ! empty( $values['page_show_navigation'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-show-related', 
								__( 'Show Related Pages', 'projectpress' ), 
								[ 
									'description' => __( 'Display related Mod pages below the current page.', 'projectpress' ), 
									'tooltip' => __( 'Related pages encourage visitors to continue exploring.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::switch( 
								'projectpress_layout[show_related_pages]', 
								'1', 
								__( 'Enable', 'projectpress' ), 
								[ 
									'id' => 'projectpress-layout-page-show-related', 
									'checked' => ! empty( $values['show_related_pages'] ) 
								] 
							); ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<?php echo FormFieldHelper::label( 
								'projectpress-layout-page-related-count', 
								__( 'Related Pages Count', 'projectpress' ), 
								[ 
									'description' => __( 'Number of related pages to display.', 'projectpress' ), 
									'tooltip' => __( 'Keep this low enough that related content does not overwhelm the page.', 'projectpress' ) 
								] 
							); ?>
						</th>
						<td>
							<?php echo FormFieldHelper::input( 
								'projectpress_layout[related_pages_count]', 
								isset( $values['related_pages_count'] ) ? (string) $values['related_pages_count'] : '4', 
								[ 
									'id' => 'projectpress-layout-page-related-count', 
									'type' => 'number', 
									'min' => 1, 
									'max' => 12, 
									'class' => 'w-100' 
								] 
							); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}
}


