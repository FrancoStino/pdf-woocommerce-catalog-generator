<?php

// Aggiungi l'opzione "Esporta in PDF" nell'elenco a discesa "Azioni di gruppo"
add_filter('bulk_actions-edit-product', 'add_export_pdf_bulk_action');
function add_export_pdf_bulk_action($bulk_actions) {
    $bulk_actions['export_pdf'] = 'Catalogo in PDF';
    return $bulk_actions;
}

// Gestisci l'azione "Esporta in PDF" delle azioni di gruppo
add_filter('handle_bulk_actions-edit-product', 'handle_export_pdf_bulk_action', 10, 3);
function handle_export_pdf_bulk_action($redirect_to, $doaction, $post_ids) {
    if ($doaction == 'export_pdf') { 
?>

<div style="display:flex;flex-direction: column;align-content: center;align-items: center;">
	
	<div style="display: flex;width: 30vw;height: 100vh;font-family: 'Poppins', sans-serif;font-weight: bold;flex-direction: column;justify-content: space-between;">
	
		<div style="margin-bottom: 20px;">
			<label for="company_name" style="display: block; margin-bottom: 5px;">Titolo:</label>
			<input type="text" id="company_name" name="company_name" style="display: block; width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
		</div>
	
		<div style="margin-bottom: 20px;">
			<?php
		
		// Define the editor settings
    $settings = array(
		'media_buttons'       => false,
		'default_editor'      => '',
		'drag_drop_upload'    => false,
		'textarea_rows'       => 20,
		'tabindex'            => '',
		'tabfocus_elements'   => ':prev,:next',
		'editor_css'          => '',
		'editor_class'        => '',
		'teeny'               => false,
		'dfw'                 => false,
		'_content_editor_dfw' => false,
		'quicktags'           => true,
		/*'tinymce' => array(
		
		'selector' => 'textarea',  // change this value according to your HTML
  'element_format'  => 'html',
  'encoding' => 'xml'
		)*/
		
    );

    // Grab content to put inside a variable
    

    // Create the editor
    wp_editor('', 'description', $settings); 

    // IMPORTANT
    // Adding the required scripts, styles, and wp_editor configuration
    _WP_Editors::enqueue_scripts();
    _WP_Editors::editor_js();
    print_footer_scripts();
			
			?>
</div>

		<label for="custom_price" style="margin-bottom: 5px; display:block">Prezzo personalizzato in %:</label>
		<div style="margin-bottom: 20px; display: flex;">
			<div style="display: flex; flex-direction: column; justify-content: center; margin-right: 10px; gap: 10px;">
				
				<input type="radio" id="addition" name="operator" value="+">
				<input type="radio" id="subtraction" name="operator" value="-">
				<label for="addition" class="option option-1">
					<span>+</span>
				</label>
				<label for="subtraction" class="option option-2">
					<span>-</span>
				</label>
			</div>
			<div style="display: flex; flex-direction: column; justify-content: center; flex: 1;">
				<input type="number" id="custom_price" name="custom_price" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
			</div>
		</div>
  
	
		<div style="margin-bottom: 20px;">
			<label for="image_upload" style="display: block; margin-bottom: 5px;">Immagine:</label>
			<input type="file" id="image_upload" name="image_upload" accept="image/*" style="display: block; width: 100%;">
		</div>


		<div style="margin-bottom: 20px;">
			<button type="button" onclick="generate_pdf(document.getElementById('company_name').value, get_tinymce_content('description'), document.getElementById('custom_price').value, document.querySelector('input[name=\'operator\']:checked').value)" style="display: block; width: 100%; padding: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer;">Genera PDF</button>
	</div>
	</div>
</div>

<script>
	
	function get_tinymce_content(id) {

		if (jQuery("#wp-"+id+"-wrap").hasClass("tmce-active")){
			return tinyMCE.get(id).getContent();
		}else{
			return jQuery("#"+id).val();
		}
		
	}
	
	
	/* ------------------------------------------- */
	
	
	function generate_pdf(company_name, description, custom_price, operator, image_upload) {
				
		var product_ids = <?php echo json_encode(array_map('intval', $post_ids)); ?>;
		var image_upload = document.getElementById('image_upload').files[0];
		var formData = new FormData();
				
		formData.append('action', 'generate_pdf');
		formData.append('product_ids', JSON.stringify(product_ids));
		formData.append('company_name', company_name);
		formData.append('description', description);
		formData.append('custom_price', custom_price);
		formData.append('operator', operator);
		formData.append('image_upload', image_upload);
		
		var xhr = new XMLHttpRequest();
		xhr.open('POST', '<?php echo esc_url(admin_url('admin-ajax.php')); ?>');
		xhr.responseType = 'blob';
		xhr.onload = function () {
			//if (xhr.status === 200) {
				var blob = new Blob([xhr.response], {type: 'application/pdf'});
				var link = document.createElement('a');
				link.href = window.URL.createObjectURL(blob);
				link.download = '<?php echo 'Catalogo '.get_bloginfo( 'name' ).' per '; ?>' + company_name +'.pdf';
				link.click();
			//}
		};
		xhr.send(formData);
	}

</script>

<style>
.option {
  background: #fff;
  display: flex;
  align-items: center;
  justify-content: space-evenly;
  cursor: pointer;
  border-radius: 5px;
  padding: 0 10px;
  border: 2px solid #180f2f;
  transition: all 0.5s ease;
  margin: 0 10px;
}
input[type="radio"] {
  display: none;
}
input#addition:checked ~ .option-1,
input#subtraction:checked ~ .option-2 {
  background: #180f2f;
  border-color: #180f2f;
}
input#addition:checked ~ .option-1 span,
input#subtraction:checked ~ .option-2 span {
  color: #fff;
}
.option span {
  font-size: 20px;
}
</style>


<?php
		exit;
	}
	return $redirect_to;
}



// Gestisci l'azione AJAX "generate_pdf"
add_action('wp_ajax_generate_pdf', 'generate_pdf_ajax');
function generate_pdf_ajax() {
       
    $product_ids = json_decode(stripslashes($_POST['product_ids']));
    $company_name = sanitize_text_field($_POST['company_name']);
	$description = stripslashes($_POST['description']);
	$custom_price = sanitize_text_field($_POST['custom_price']);
	$operator = $_POST['operator'];
	
	
	// Load MPDF library
    require_once(get_stylesheet_directory() . '/functions/pdf-catalog-generator-woocommerce/mpdf/vendor/autoload.php');
    $mpdf = new \Mpdf\Mpdf();
	
	
	$mpdf->SetDefaultFont('Poppins'); // Imposta il font di fallback predefinito

	$mpdf->SetDefaultBodyCSS('background', "url('https://www.concortesia.it/wp-content/uploads/2023/07/carta-intestata-pdf.svg')");
	$mpdf->SetDefaultBodyCSS('background-image-resize', 5);
	$mpdf->SetDefaultBodyCSS('background-image-opacity', 0.3);

	$mpdf->setAutoBottomMargin = 'stretch';
	$mpdf->setAutoTopMargin = 'stretch';
	
    // Set PDF properties
    $mpdf->SetTitle('Catalogo '.get_bloginfo( 'name' ).' per '.$company_name);
    $mpdf->SetAuthor(get_bloginfo( 'name' ));
    $mpdf->SetCreator(get_bloginfo( 'name' ));
    $mpdf->SetDisplayMode('fullpage');
	
	$custom_logo_id = get_theme_mod( 'custom_logo' );
	$custom_logo_url = wp_get_attachment_image_url( $custom_logo_id, 'full' );
	
	
	// Creazione dell'immagine nel PDF
	if (!empty($_FILES['image_upload']['tmp_name'])) {
		$uploaded_image = $_FILES['image_upload']['tmp_name'];
		$image_data = file_get_contents($uploaded_image);
	}
	
	// Header presentation
	$mpdf->SetHTMLHeader('<div style="text-align:center; padding-bottom: 20px; margin-bottom: 10px;"><img src="' . esc_url( $custom_logo_url ) . '" alt="Logo" style="width: 30%"></div>');


    // Add company presentation page
    $mpdf->AddPage();
	
	
	// Presentation first page
	$height_page = $mpdf->hPt;
	$width_page = $mpdf->wPt;

	
	
	// Compressione dell'immagine
	$quality = 80; // Imposta la qualità della compressione (valore compreso tra 0 e 100)

	$source_image = imagecreatefromstring(file_get_contents($_FILES['image_upload']['tmp_name']));
	ob_start(); // Avvia il buffer di output
	
	// Determina l'estensione dell'immagine originale
	$image_info = getimagesize($_FILES['image_upload']['tmp_name']);
	$extension = image_type_to_extension($image_info[2], false);

	if ($extension === 'png') {
		imagepng($source_image, NULL, round(9 * $quality / 100)); // Salva l'immagine compressa come file PNG nel buffer di output
	} else {
		imagejpeg($source_image, NULL, $quality); // Salva l'immagine compressa come file JPEG nel buffer di output
	}

	$compressed_image_data = ob_get_clean(); // Ottieni i dati compressi dal buffer di output
	imagedestroy($source_image);
	
	// Crea un file temporaneo
	$temp_file = tmpfile();
	$temp_file_path = stream_get_meta_data($temp_file)['uri'];
	
	// Scrivi i dati dell'immagine compressa nel file temporaneo
	file_put_contents($temp_file_path, $compressed_image_data);
	
	// Ora puoi utilizzare il file temporaneo $temp_file_path per aggiungere l'immagine al PDF come indicato in precedenza

	$presentation_first_page = '
	<table style="table-layout: fixed; width: ' . $width_page . 'pt; margin: 0; padding: 0;" cellpadding="0" cellspacing="0">
	<tr>
    <td style="word-wrap: break-word; overflow-wrap: break-word; height: ' . $height_page . 'pt; vertical-align: middle; padding: 0px 5px; margin: 0; text-align: center;">
	<img src="' . $temp_file_path . '" alt="Immagine" style="width: 50%;">
	<br><br><br><br>
	<h1>' . $company_name . '</h1>
	<br><br>
	<h3 style="text-align: left !important; font-weight: normal;">' . $description . '</h3>
	</td>
	</tr>
	</table>';
	
	

	$mpdf->WriteHTML($presentation_first_page);
	
	

	fclose($temp_file); // Chiudi il file temporaneo
	unlink($temp_file_path); // Elimina il file temporaneo
	
	// Recurring Header after presentation
	$mpdf->SetHTMLHeader('<div style="text-align:center; border-bottom: 1px solid; padding-bottom: 20px; margin-bottom: 10px;"><img src="' . esc_url( $custom_logo_url ) . '" alt="Logo" style="width: 30%"></div>');

	
    // Loop through selected products and group them by category
    $products_by_category = array();
    foreach ($product_ids as $product_id) {
        $product = wc_get_product($product_id);
        if (!$product) {
            continue;
        }
        $categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'all'));
        if (empty($categories)) {
            $category_name = __('Uncategorized', 'textdomain');
        } else {
            $category_name = $categories[0]->name;
        }
        if (!isset($products_by_category[$category_name])) {
            $products_by_category[$category_name] = array();
        }
        $products_by_category[$category_name][] = $product;
    }

    // Loop through categories and add them to PDF
    $page_number = 1;
    foreach ($products_by_category as $category_name => $category_products) {
        // Add new page for category
        $mpdf->AddPage();
        $mpdf->SetHTMLFooter('Pagina ' . $page_number);
        $page_number++;
        $mpdf->WriteHTML('<h2>' . $category_name . '</h2>');

        // Add table with products
        $mpdf->WriteHTML('<table style="width:100%; border-collapse: collapse; background:#fff;">');
        foreach ($category_products as $product) {
			// Prezzo variabile
			$charge = ($custom_price / 100) * $product->get_price();
			if ($operator == '-')
				$final_price = $product->get_price() - $charge;
			else
				$final_price = $product->get_price() + $charge;
			// Immagine
            $image_url = wp_get_attachment_image_src($product->get_image_id(), 'large');
			$mpdf->WriteHTML('<tr>');
			// Colonna tabella immagine
			$mpdf->WriteHTML('<td style="border: 1px solid black; text-align: center;" width="30%">');
			$mpdf->WriteHTML('<img width="30%" style="display:block;"  src="' . $image_url[0] . '" alt="' . $product->get_name() . '">');
			$mpdf->WriteHTML('</td>');
			// Colonna tabella titolo, descrizione e prezzo
			$mpdf->WriteHTML('<td style="border: 1px solid black; padding: 15px;">');
            $mpdf->WriteHTML('<h2>' . $product->get_name() . '</h2><hr><p>' . $product->get_short_description() . '</p><hr><h3>Prezzo: ' . number_format($final_price, 2, ',', '.') . ' € <small>IVA Esclusa</small></h3>');
			$mpdf->WriteHTML('</td>');
			$mpdf->WriteHTML('</tr>');
        }
        $mpdf->WriteHTML('</table>');
    }
        $mpdf->Output('Catalogo '.get_bloginfo( 'name' ).' per '.$company_name, 'D');
        exit;
    }
