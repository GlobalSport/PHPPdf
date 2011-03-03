<?php

use PHPPdf\Font\Font;

class FontTest extends PHPUnit_Framework_TestCase
{
    private $font;
    private $fontPath;

    public function setUp()
    {
        $fontPath = __DIR__.'/../resources';
        $this->fontPath = $fontPath;
        $this->font = new Font(array(
            Font::STYLE_NORMAL => \Zend_Pdf_Font::fontWithPath($fontPath.'/verdana.ttf'),
            Font::STYLE_BOLD => \Zend_Pdf_Font::fontWithPath($fontPath.'/verdanab.ttf'),
            Font::STYLE_ITALIC => \Zend_Pdf_Font::fontWithPath($fontPath.'/verdanai.ttf'),
            Font::STYLE_BOLD_ITALIC => \Zend_Pdf_Font::fontWithPath($fontPath.'/verdanaz.ttf'),
        ));
    }

    /**
     * @test
     */
    public function switchingDecorationStyle()
    {
        $font = $this->font->getFont();

        $this->assertFalse($font->isBold() || $font->isItalic());

        $this->font->setStyle(Font::STYLE_BOLD);
        $font = $this->font->getFont();
        
        $this->assertTrue($font->isBold());
        $this->assertFalse($font->isItalic());

        $this->font->setStyle(Font::STYLE_ITALIC);
        $font = $this->font->getFont();

        $this->assertFalse($font->isBold());
        $this->assertTrue($font->isItalic());

        $this->font->setStyle(Font::STYLE_ITALIC | Font::STYLE_BOLD);
        $font = $this->font->getFont();

        $this->assertTrue($font->isBold() && $font->isItalic());

        $this->font->setStyle(Font::STYLE_BOLD_ITALIC);
        $font = $this->font->getFont();

        $this->assertTrue($font->isBold() && $font->isItalic());

        $this->font->setStyle(Font::STYLE_NORMAL);
        $font = $this->font->getFont();

        $this->assertFalse($font->isBold() || $font->isItalic());
    }

    /**
     * @test
     */
    public function switchingDecorationStyleByString()
    {
        $this->font->setStyle('bold');
        $font = $this->font->getFont();
        
        $this->assertTrue($font->isBold());
        $this->assertFalse($font->isItalic());

        $this->font->setStyle('italic');
        $font = $this->font->getFont();

        $this->assertFalse($font->isBold());
        $this->assertTrue($font->isItalic());

        $this->font->setStyle('normal');
        $font = $this->font->getFont();
        $this->assertFalse($font->isBold() || $font->isItalic());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function creationWithEmptyArray()
    {
        new Font(array());
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function creationWithInvalidFontTypes()
    {
        new Font(array(
            Font::STYLE_BOLD => \Zend_Pdf_Font::fontWithPath($this->fontPath.'/verdana.ttf'),
            Font::STYLE_NORMAL => \Zend_Pdf_Font::fontWithPath($this->fontPath.'/verdana.ttf'),
            8 => \Zend_Pdf_Font::fontWithPath($this->fontPath.'/verdana.ttf'),
        ));
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function creationWithoutNormalFont()
    {
        new Font(array(
            Font::STYLE_BOLD => \Zend_Pdf_Font::fontWithPath($this->fontPath.'/verdana.ttf'),
        ));
    }
}