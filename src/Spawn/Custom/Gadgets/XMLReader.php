<?php declare(strict_types=1);

namespace spawnCore\Custom\Gadgets;

class XMLReader
{


    public static function readFile(string $filePath): XMLContentModel
    {
        $xml = FileEditor::getFileContent(URIHelper::pathifie($filePath));

        if ($xml === false) {
            dump('Warning! Could not import path ' . $filePath);
            return new XMLContentModel("empty");
        }

        $xmlContent = simplexml_load_string($xml);

        $rootContainer = new XMLContentModel($xmlContent->getName());

        $rootContainer->loadFromSimpleXMLElement($xmlContent, $filePath);

        return $rootContainer;
    }


}