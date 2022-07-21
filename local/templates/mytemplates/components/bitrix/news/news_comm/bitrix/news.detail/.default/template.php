<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);

$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();

$star_id = $request->getpost('star_id');
$title = trim(htmlspecialchars($request->getpost('title')));
$text_comment = trim(htmlspecialchars($request->getpost('comment_text')));
$element_id = $request->getpost('element_id');

$el = new CIBlockElement;

$PROP = array();
$PROP[4] = $star_id;  // свойству с кодом 12 присваиваем значение "Белый"
$PROP[3] = $element_id;        // свойству с кодом 3 присваиваем значение 38

$arLoadProductArray = Array(
    "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
    "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
    "IBLOCK_ID"      => 2,
    "PROPERTY_VALUES"=> $PROP,
    "NAME"           => $title,
    "ACTIVE"         => "Y",            // активен
    "PREVIEW_TEXT"   => $text_comment,
    "DETAIL_TEXT"    => "",
);

if($PRODUCT_ID = $el->Add($arLoadProductArray)) {
    echo "New ID: ".$PRODUCT_ID;
}

if($request->getPost('flag') == 1) {
    $APPLICATION->RestartBuffer();
    echo 'ok';
}
if($request->getPost('flag') == 1) die;
?>
<div class="news-detail">
	<?if($arParams["DISPLAY_PICTURE"]!="N" && is_array($arResult["DETAIL_PICTURE"])):?>
		<img
			class="detail_picture"
			border="0"
			src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
			width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
			height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
			alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
			title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
			/>
	<?endif?>
	<?if($arParams["DISPLAY_DATE"]!="N" && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<span class="news-date-time"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></span>
	<?endif;?>
	<?if($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<h3><?=$arResult["NAME"]?></h3>
	<?endif;?>
	<?if($arParams["DISPLAY_PREVIEW_TEXT"]!="N" && $arResult["FIELDS"]["PREVIEW_TEXT"]):?>
		<p><?=$arResult["FIELDS"]["PREVIEW_TEXT"];unset($arResult["FIELDS"]["PREVIEW_TEXT"]);?></p>
	<?endif;?>
	<?if($arResult["NAV_RESULT"]):?>
		<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
		<?echo $arResult["NAV_TEXT"];?>
		<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
	<?elseif($arResult["DETAIL_TEXT"] <> ''):?>
		<?echo $arResult["DETAIL_TEXT"];?>
	<?else:?>
		<?echo $arResult["PREVIEW_TEXT"];?>
	<?endif?>
	<div style="clear:both"></div>
	<br />
	<?foreach($arResult["FIELDS"] as $code=>$value):
		if ('PREVIEW_PICTURE' == $code || 'DETAIL_PICTURE' == $code)
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?
			if (!empty($value) && is_array($value))
			{
				?><img border="0" src="<?=$value["SRC"]?>" width="<?=$value["WIDTH"]?>" height="<?=$value["HEIGHT"]?>"><?
			}
		}
		else
		{
			?><?=GetMessage("IBLOCK_FIELD_".$code)?>:&nbsp;<?=$value;?><?
		}
		?><br />
	<?endforeach;
	foreach($arResult["DISPLAY_PROPERTIES"] as $pid=>$arProperty):?>

		<?=$arProperty["NAME"]?>:&nbsp;
		<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
			<?=implode("&nbsp;/&nbsp;", $arProperty["DISPLAY_VALUE"]);?>
		<?else:?>
			<?=$arProperty["DISPLAY_VALUE"];?>
		<?endif?>
		<br />
	<?endforeach;
	if(array_key_exists("USE_SHARE", $arParams) && $arParams["USE_SHARE"] == "Y")
	{
		?>
		<div class="news-detail-share">
			<noindex>
			<?
			$APPLICATION->IncludeComponent("bitrix:main.share", "", array(
					"HANDLERS" => $arParams["SHARE_HANDLERS"],
					"PAGE_URL" => $arResult["~DETAIL_PAGE_URL"],
					"PAGE_TITLE" => $arResult["~NAME"],
					"SHORTEN_URL_LOGIN" => $arParams["SHARE_SHORTEN_URL_LOGIN"],
					"SHORTEN_URL_KEY" => $arParams["SHARE_SHORTEN_URL_KEY"],
					"HIDE" => $arParams["SHARE_HIDE"],
				),
				$component,
				array("HIDE_ICONS" => "Y")
			);
			?>
			</noindex>
		</div>
		<?
	}
	?>
</div>

<div>
    <div class="d-flex center_div">
        <div class="comment_new active_text">Отзывы покупателей</div>
        <div class="comment_wirte">Оставить отзыв</div>
    </div>

    <div class="comments ">

        <?
        $res = CIBlockElement::GetList(Array(), array("IBLOCK_ID" => 2), false, Array(), array("*"));

        $reding = 0;
        $quantity = 0;
        while($ob = $res->GetNextElement())
        {

            $arFields = $ob->GetFields();
            $arProps = $ob->GetProperties();
//            echo " <pre>";
//            print_r($arFields);
//            print_r($arProps);

            if ($arProps["ID_ELEMENT"]["VALUE"] === $arResult["ID"]):
                $quantity += 1;
                $reding += $arProps["RATING"]["VALUE"]

            ?>

            <div class="comment_block">
                <h3><?=$arFields["NAME"]?></h3>

                <p><?=$arFields["PREVIEW_TEXT"]?></p>
            </div>

        <?
            endif;
        }
        ?>

        <div class="appand_div"></div>
        <?
        $reting_result = $reding / $quantity;
        ?>

        <div>
            <p class="reting_result">Рейтинг: <span><?=round($reting_result, 2)?></span> </p>
        </div>

    </div>

    <div class="form_comment d-none">
        <div class="d-flex">
            <div>
                <img
                        class="detail_picture_comment"
                        border="0"
                        src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
                        width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
                        height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
                        alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
                        title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>"
                />
            </div>
            <div>
                <h3><?=$arResult["NAME"]?></h3>
            </div>
        </div>

        <form>
            <input type="hidden" name="ID_ELEMENT" id="id_element" value="<?=$arResult["ID"]?>">
            <input type="hidden" name="ID_USER" value="<?=$USER->GetID()?>">

            <div class="rating_vote">
                <p>Общий рейтинг</p>
                <p>
                    <img src="image/star.svg" data-id="1" alt="#" class="selected_star">
                    <img src="image/star.svg" data-id="2" alt="#" class="selected_star">
                    <img src="image/star.svg" data-id="3" alt="#" class="selected_star">
                    <img src="image/star.svg" data-id="4" alt="#" class="selected_star">
                    <img src="image/star.svg" data-id="5" alt="#" class="selected_star">
                </p>
            </div>
            <lable>
                <p>Заголовок</p>
                <input type="text" id="title_comment" name="NAME_COMMENT">
            </lable>

            <lable>
                <p>Текст отзыва</p>
                <textarea name="COMMNET"  id="comment_text" cols="30" rows="10" >
                </textarea>
            </lable><br>
            <input type="submit" class="submit">
        </form>
    </div>

</div>

