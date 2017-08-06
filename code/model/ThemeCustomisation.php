<?php



class ThemeCustomisation extends DataObject {

    private static $location_for_scss_file = 'themes/main_mysite/src/variables/_themecustomisation.scss';

    private static $singular_name = 'Theme Customisation';

    function i18n_singular_name()
    {
        return _t('ThemeCustomiser.SINGULAR_NAME', 'Theme Customisation');
    }

    private static $plural_name = 'Theme Customisations';

    function i18n_plural_name()
    {
        return _t('ThemeCustomiser.PLURAL_NAME', 'Theme Customisations');
    }

    private static $db = [
        'BackgroundColour' => 'Color',
        'FontColour' => 'Color',
        'Accent1Colour' => 'Color',
        'Accent2Colour' => 'Color',
        'MenuBarBackgroundColour' => 'Color',
        'LinkColour' => 'Color',
        'ActiveColour' => 'Color',
        'HeaderFont' => 'Varchar(50)',
        'TextFont' => 'Varchar(50)',
        'MonoFont' => 'Varchar(50)'
    ];

    private static $has_one = [
        'LargeLogo' => 'Image',
        'SmallLogo' => 'Image'
    ];

    private static $summary_fields = [
        'LargeLogo.Stripthumbnail' => 'Name'
    ];

    private static $field_labels = [
        'MonoFont' => 'Mono spaced font'
    ];

    private static $field_labels_right = [
        'MonoFont' => 'Font similar to courier with identical width used for each character - great for numbers.'
    ];


    public function CMSEditLink()
    {
        $controller = singleton("ThemeCustomiser");

        return $controller->Link().$this->ClassName."/EditForm/field/".$this->ClassName."/item/".$this->ID."/edit";
    }

    public function CMSAddLink()
    {
        $controller = singleton("ThemeCustomiser");

        return $controller->Link().$this->ClassName."/EditForm/field/".$this->ClassName."/item/new";
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fontList = Injector::inst()->get('GoogleFontProvider')->fullList();
        $fontFields = $this->Config()->get('font_fields');
        $labels = $this->FieldLabels();
        foreach($this->fontFields() as $fontField) {
            $fields->replaceField(
                $fontField,
                DropdownField::create(
                    $fontField,
                    $labels[$fontField],
                    ['' => '-- select --'] + $fontList
                )
            );
        }
        foreach($this->colourFields() as $colourField) {
            $fields->replaceField(
                $colourField,
                ColorField::create(
                    $colourField,
                    $labels[$colourField]
                )
            );
        }

        $rightFieldDescriptions = $this->Config()->get('field_labels_right');
        foreach($rightFieldDescriptions as $field => $desc) {
            $field = $fields->DataFieldByName($field);
            if($field) {
                $field->setDescription($desc);
            }
        }

        return $fields;
    }

    /**
     * exports to variable SCSS file ...
     */
    function exportToThemeSCSS()
    {

        $location = Director::baseFolder() .'/'. $this->Config()->get('location_for_scss_file');
        $data = $this->renderWith('ThemeCustomisationSCSSOutput');
        file_put_contents($location, $data);
    }

    /**
     * stuff to be inserted into head of html
     * - custom css
     * - link to google fonts ...
     */
    function htmlForPageHeader()
    {
        $list = [];
        foreach($this->fontFields() as $fontField) {
            $list[$this->$fontField] = $this->$fontField;
        }
        $fontList = Injecton::inst()->get('GoogleFontProvider')->getLink($list);

        return $fontList;
    }

    function canCreate($member = null)
    {
        if(ThemeCustomisation::get()->count()) {
            return false;
        }

        return parent::canCreate($member);
    }

    function canDelete($member = null)
    {
        return false;
    }

    protected function fontFields()
    {
        return $this->fieldsPerType('Font');
    }

    protected function colourFields()
    {
        return $this->fieldsPerType('Colour');
    }

    protected function fieldsPerType($name)
    {
        $list = $this->Config()->get('db');
        foreach($list as $key => $type) {
            if(substr($key, strlen($name) * -1) === $name) {
                $newList[$key] = $key;
            }
        }
        return $newList;
    }

}
