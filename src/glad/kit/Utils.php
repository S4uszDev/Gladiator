<?php

namespace glad\kit;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;

class Utils
{
    public static function cereal(array $contents)
    {
        $result = [];
        foreach ($contents as $slot => $item) {
            $result[$slot] = $item->getVanillaName() . ':' . $item->getCount();
            $enchants = $item->getEnchantments();
            $size = count($enchants);
            $enchantStr = '';

            if ($size > 0) {
                $size--;
                $count = 0;
                foreach ($enchants as $enchant) {
                    $comma = $count === $size ? '' : ',';
                    $str = EnchantmentIdMap::getInstance()->toId($enchant->getType()) . ":{$enchant->getLevel()}";
                    $enchantStr .= $str . $comma;
                    $count++;
                }
                $result[$slot] .= "-$enchantStr"; // Move esta linha para fora do loop dos encantamentos
            }
        }
        return $result;
    }

    public static function milk(array $contents): array
    {
        $result = [];
        $itemParser = StringToItemParser::getInstance();
        foreach ($contents as $slot => $item) {
            $data = explode(":", $item);
            $split = explode('-', $item);
            $itemPortion = strval($split[0]);
            $enchants = [];

            $itemParsed = $itemParser->parse($data[0]);
            $itemParsed->setCount((int)$data[1]);

            if (isset($split[1])) {
                $enchantPortion = strval($split[1]);
                $enchantsSplit = explode(',', $enchantPortion);
                foreach ($enchantsSplit as $e) {
                    $enchantData = explode(':', strval($e));
                    if (isset($enchantData[0], $enchantData[1])) {
                        $enchantID = intval($enchantData[0]);
                        $level = intval($enchantData[1]);

                        $enchant = new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId($enchantID), $level);
                        $enchants[] = $enchant;
                    }
                }

                if (count($enchants) > 0) {
                    foreach ($enchants as $ench) {
                        if ($itemParsed instanceof Item) {
                            $itemParsed->addEnchantment($ench);
                        }
                    }
                    $result[$slot] = $itemParsed;
                }
            } else {
                $result[$slot] = $itemParsed;
            }
        }
        return $result;
    }
}