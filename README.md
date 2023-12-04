# PDF-and-mPDF
# Document Generator

A simple class along with the `index.php` file for creating official letters directed to government offices. The class utilizes mPDF, Smarty, and free fonts. The document is divided into defined blocks, and the rest is loaded from an HTML file. The document number is generated from the institution's name. Each document is also accompanied by a barcode representing the document number.

## Installation

To install the necessary tools, use Composer:

composer install

Directory create: You need to create a folder: 
- temp, 
- templates_c, 
- generated 
and give them write permissions via the script.

Usage

    Configure the institution`s name and other data in the config/config.json file.
    Run the index.php script to generate official letters with unique document numbers and barcodes.
    You can run index.php without changing anything. The configuration file contains everything you need.
    If you need to load data in html form, the file to be loaded is in the content/content.html folder.

Dependencies

    mPDF: A PHP library for creating PDF documents.
    Smarty: A template engine for PHP.
    Free Fonts: High-quality fonts used in document generation. (included in folder /fonts)

License

This project is licensed under the MIT License + NoGov - see the LICENSE.md file for details.
