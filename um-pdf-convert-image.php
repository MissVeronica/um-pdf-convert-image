<?php
/**
 * Plugin Name:     Ultimate Member - PDF convert Image
 * Description:     Extension to Ultimate Member for converting first page of an uploaded PDF file to an Image.
 * Version:         1.1.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
if ( ! class_exists( 'UM' ) ) return;

class UM_PDF_Convert_Image {

    public $convert_pdf_image      = '';
    public $resolution             = '';
    public $pdf_image_width        = '';
    public $convert_pdf_image_type = '';
    public $convert_pdf_upload     = '';

    function __construct() {

        add_filter( 'um_settings_structure',            array( $this, 'um_settings_structure_convert_pdf' ), 10, 1 );
        add_filter( 'um_user_pre_updating_files_array', array( $this, 'um_user_pre_updating_files_array_convert_pdf' ), 10, 1 );
    }

    public function get_convert_pdf_options() {

        $this->resolution = absint( trim( sanitize_text_field( UM()->options()->get( 'convert_pdf_resolution' ))));
        if ( empty( $this->resolution )) {
            $this->resolution = 400;
        }

        $this->pdf_image_width = absint( trim( sanitize_text_field( UM()->options()->get( 'convert_pdf_image_width' ))));
        if ( empty( $this->pdf_image_width ) ) {
            $this->pdf_image_width = 1000;
        }

        $this->convert_pdf_image_type = sanitize_text_field( UM()->options()->get( 'convert_pdf_image_type' ));
        $this->convert_pdf_image      = sanitize_text_field( UM()->options()->get( 'convert_pdf_image' ));
        $this->convert_pdf_upload     = sanitize_text_field( UM()->options()->get( 'convert_pdf_upload' ));
    }

    public function um_user_pre_updating_files_array_convert_pdf( $files ) {

        $this->get_convert_pdf_options();

        if ( isset( $files[$this->convert_pdf_upload] ) && ! empty( $files[$this->convert_pdf_upload] )) {

            if ( $files[$this->convert_pdf_upload]  == 'empty_file' ) {

                um_fetch_user( um_profile_id() );
                $image_file = UM()->uploader()->get_upload_base_dir() . um_profile_id() . DIRECTORY_SEPARATOR . um_user( $this->convert_pdf_image );
                unlink( $image_file );
                update_user_meta( um_profile_id(), $this->convert_pdf_image, '' );

            } else {

                $path_pdf_file = UM()->uploader()->get_core_temp_dir() . DIRECTORY_SEPARATOR;
                $pdf_image_name = str_replace( 'file_', 'stream_photo_', $files[$this->convert_pdf_upload] ) . '.' . $this->convert_pdf_image_type;

                $imagick = new Imagick();
                $imagick->setResolution( $this->resolution, $this->resolution );

                $imagick->readImage( $path_pdf_file . $files[$this->convert_pdf_upload] . '[0]' );                

                $imagick->setImageFormat( $this->convert_pdf_image_type );
                $imagick->setImageBackgroundColor( '#ffffff' );
                $imagick->setImageAlphaChannel(11);
                $imagick = $imagick->mergeImageLayers( Imagick::LAYERMETHOD_FLATTEN );

                $size = $imagick->getImageGeometry();
                $new_height = ceil( $this->pdf_image_width * ( $size['height'] / $size['width'] ) );
                $imagick->resizeImage( $this->pdf_image_width, $new_height, Imagick::FILTER_CUBIC, 1, true );

                $imagick->writeImages( $path_pdf_file . $pdf_image_name, false );
                $imagick->clear(); 
                $imagick->destroy();

                $files[$this->convert_pdf_image] = $pdf_image_name;
            }
        }

        return $files;
    }

    public function um_settings_structure_convert_pdf( $settings_structure ) {

        $settings_structure['']['sections']['uploads']['fields'][] =

                array(
                        'id'          => 'convert_pdf_upload',
                        'type'        => 'text',
                        'label'       => __( 'Convert PDF to an Image - PDF file meta_key', 'ultimate-member' ),
                        'tooltip'     => __( 'Enter the upload PDF file meta_key name.', 'ultimate-member' ),
                        'size'        => 'small',
                    );

        $settings_structure['']['sections']['uploads']['fields'][] =

                array(
                        'id'          => 'convert_pdf_image',
                        'type'        => 'text',
                        'label'       => __( 'Convert PDF to an Image - Image file meta_key', 'ultimate-member' ),
                        'tooltip'     => __( 'Enter the upload PDF file converted Image file meta_key name.', 'ultimate-member' ),
                        'size'        => 'small',
                    );

        $settings_structure['']['sections']['uploads']['fields'][] =

                array(
                        'id'          => 'convert_pdf_resolution',
                        'type'        => 'text',
                        'label'       => __( 'Convert PDF to an Image - Resolution', 'ultimate-member' ),
                        'tooltip'     => __( 'Enter the image conversion resolution. The higher integer value better resolution but also longer processing time for the conversion. Default value 400.', 'ultimate-member' ),
                        'size'        => 'small',
                    );

        $settings_structure['']['sections']['uploads']['fields'][] =

                array(
                        'id'          => 'convert_pdf_image_width',
                        'type'        => 'text',
                        'label'       => __( 'Convert PDF to an Image - Converted Image width', 'ultimate-member' ),
                        'tooltip'     => __( 'Enter the Converted Image width in pixels for image full page view. Default value 1000.', 'ultimate-member' ),
                        'size'        => 'small',
                    );

        $settings_structure['']['sections']['uploads']['fields'][] =

                array(
                        'id'          => 'convert_pdf_image_type',
                        'type'        => 'select',
                        'label'       => __( 'Convert PDF to an Image - Converted Image type', 'ultimate-member' ),
                        'tooltip'     => __( 'Select the Converted Image type. Best compression with a WEBP image.', 'ultimate-member' ),
                        'size'        => 'small',
                        'options'     => array( 'jpg'  => 'JPG',
                                                'png'  => 'PNG',
                                                'webp' => 'WEBP',
                                               ),
                    );

        return $settings_structure;
    }
}

new UM_PDF_Convert_Image();

