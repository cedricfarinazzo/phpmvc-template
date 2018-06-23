<?php

abstract class BBCode
{
    public static function BBCodeGetEditor($aName, $aTxt, $aButtonLst = '', $row = 0, $col = 0)
    {
        $tag = array();
        $tag['fontsize'] = array('id' => 'bbcode1', 'title' => 'Taille', 'type' => 'select', 'option' => [['title' => 'Dimension', 'value' => ''], ['title' => '10px', 'value' => '10'], ['title' => '11px', 'value' => '11'], ['title' => '12px', 'value' => '12'], ['title' => '14px', 'value' => '14'], ['title' => '16px', 'value' => '16'], ['title' => '18px', 'value' => '18'], ['title' => '20px', 'value' => '20'], ['title' => '24px', 'value' => '24']], 'tag1' => '[size=$1]', 'tag2' => '[/size]');
        $tag['color'] = array('id' => 'bbcode2', 'title' => 'Couleur', 'type' => 'select', 'option' => [['title' => 'Couleur', 'value' => ''], ['title' => 'Rouge', 'value' => '#ff0000'], ['title' => 'Vert', 'value' => '#00ff00'], ['title' => 'Bleu', 'value' => '#0000ff']], 'tag1' => '[color=$1]', 'tag2' => '[/color]');
        $tag['bold'] = array('id' => 'bbcode3', 'title' => 'Gras', 'type' => 'button', 'tag1' => '[b]', 'tag2' => '[/b]');
        $tag['italic'] = array('id' => 'bbcode4', 'title' => 'Italique', 'type' => 'button', 'tag1' => '[i]', 'tag2' => '[/i]');
        $tag['underline'] = array('id' => 'bbcode5', 'title' => 'Souligné', 'type' => 'button', 'tag1' => '[u]', 'tag2' => '[/u]');
        $tag['stroke'] = array('id' => 'bbcode6', 'title' => 'Barré', 'type' => 'button', 'tag1' => '[s]', 'tag2' => '[/s]');
        $tag['sup'] = array('id' => 'bbcode7', 'title' => 'Exposant', 'type' => 'button', 'tag1' => '[sup]', 'tag2' => '[/sup]');
        $tag['sub'] = array('id' => 'bbcode8', 'title' => 'Indice', 'type' => 'button', 'tag1' => '[sub]', 'tag2' => '[/sub]');
        $tag['left'] = array('id' => 'bbcode9', 'title' => 'Aligné à gauche', 'type' => 'button', 'tag1' => '[left]', 'tag2' => '[/left]');
        $tag['right'] = array('id' => 'bbcode10', 'title' => 'Aligné à droite', 'type' => 'button', 'tag1' => '[right]', 'tag2' => '[/right]');
        $tag['center'] = array('id' => 'bbcode11', 'title' => 'Centrer', 'type' => 'button', 'tag1' => '[center]', 'tag2' => '[/center]');
        $tag['justify'] = array('id' => 'bbcode12', 'title' => 'Justifier', 'type' => 'button', 'tag1' => '[justify]', 'tag2' => '[/justify]');
        $tag['img'] = array('id' => 'bbcode13', 'title' => 'Image(lien)', 'type' => 'button', 'tag1' => '[img]', 'tag2' => '[/img]');
        $tag['video'] = array('id' => 'bbcode18', 'title' => 'Vidéo', 'type' => 'button', 'tag1' => '[video]', 'tag2' => '[/video]');
        $tag['url'] = array('id' => 'bbcode14', 'title' => 'View', 'type' => 'button', 'tag1' => '[url]', 'tag2' => '[/url]');
        $tag['email'] = array('id' => 'bbcode16', 'title' => 'Email', 'type' => 'button', 'tag1' => '[email]', 'tag2' => '[/email]');
        $tag['code'] = array('id' => 'bbcode15', 'title' => 'Code', 'type' => 'button', 'tag1' => '[code]', 'tag2' => '[/code]');
        $tag['quote'] = array('id' => 'bbcode17', 'title' => 'Citation', 'type' => 'button', 'tag1' => '[quote]', 'tag2' => '[/quote]');
        $tag['br'] = array('id' => 'bbcode18', 'title' => 'Saut de ligne', 'type' => 'button', 'tag1' => '[br]', 'tag2' => '');
        $tagSel = array();
        if (empty($aButtonLst)) {
            $tagSel = $tag;
        } else {
            foreach ($aButtonLst as $v) {
                if (!empty($tag[$v])) $tagSel[] = $tag[$v];
            }
        }
        $h = '<br/><div class="button-message">"';
        foreach ($tagSel as $v) {
            switch ($v['type']) {
                case 'button':
                    $h .= '<input id="' . $v['id'] . '" type="button" value="' . $v['title'] . '" onclick="EditorTagInsert(\'' . $aName . '\', \'' . $v['tag1'] . '\', \'' . $v['tag2'] . '\', 0);" />';
                    break;
                case 'select' :
                    $h .= '<select id="' . $v['id'] . '" onchange="EditorTagInsert(\'' . $aName . '\', \'' . $v['tag1'] . '\', \'' . $v['tag2'] . '\', this.value);">';
                    foreach ($v['option'] as $v) {
                        $h .= '<option value="' . $v['value'] . '">' . $v['title'] . '</option>';
                    }
                    $h .= '</select>';
                    break;
            }
        }
        $h .= '</div><br/>';
        if ($col != 0 AND $row != 0) {
            $h .= '<textarea id="' . $aName . '" name="' . $aName . '" rows="' . $row . '" cols="' . $col . '" placeholder="' . $aTxt . '"></textarea>';
        } else {
            $h .= '<label for="' . $aName . '">' . $aTxt . '</label><input type="text" id="' . $aName . '" name="' . $aName . '" />';
        }
        $h .= ' <script type="text/javascript">';
        $h .= "function EditorTagInsert(aId, aTag1, aTag2, aOpt){ 	";
        $h .= "if(aOpt === '') return 0; ";
        $h .= 'if(aOpt != 0) aTag1 = aTag1.replace("$1", aOpt); ';
        $h .= "var e = document.getElementById(aId);
		if(typeof(e) == 'undefined' || e == null) return 0; 
		var s1 = e.selectionStart;
		var s2 = e.selectionEnd;
		var txt = e.value;
		var TagLength = aTag1.length + aTag2.length;
		e.value = (txt.substring(0, s1) + aTag1 + txt.substring(s1, s2) + aTag2 + txt.substring(s2, txt.length)); 
		e.focus();
		}
		</script>";
        return $h;
    }

    public static function BBCode2Html($aTxt)
    {
        $aTxt = nl2br($aTxt);
        $tag = array('/\[b\](.*?)\[\/b\]/is', '/\[i\](.*?)\[\/i\]/is', '/\[u\](.*?)\[\/u\]/is', '/\[s\](.*?)\[\/s\]/is', '/\[sup\](.*?)\[\/sup\]/is', '/\[sub\](.*?)\[\/sub\]/is', '/\[size\=(.*?)\](.*?)\[\/size\]/is', '/\[color\=(.*?)\](.*?)\[\/color\]/is', '/\[code\](.*?)\[\/code\]/is', '/\[quote\](.*?)\[\/quote\]/is', '/\[quote\=(.*?)\](.*?)\[\/quote\]/is', '/\[left](.*?)\[\/left\]/is', '/\[right](.*?)\[\/right\]/is', '/\[center](.*?)\[\/center\]/is', '/\[justify](.*?)\[\/justify\]/is', '/\[list\](.*?)\[\/list\]/is', '/\[list=1\](.*?)\[\/list\]/is', '/\[\*\](.*?)(\n|\r\n?)/is', '/\[img\](.*?)\[\/img\]/is', '/\[url\](.*?)\[\/url\]/is', '/\[url\=(.*?)\](.*?)\[\/url\]/is', '/\[email\](.*?)\[\/email\]/is', '/\[email\=(.*?)\](.*?)\[\/email\]/is', '/\[br\]/is');
        $h = array('<strong>$1</strong>', '<em>$1</em>', '<u>$1</u>', '<span style="text-decoration:line-through;">$1</span>', '<sup>$1</sup>', '<sub>$1</sub>', '<span style="font-size:$1px;">$2</span>', '<span style="color:$1;">$2</span>', '<code><pre>$1</pre></code>', '<blockquote>$1</blockquote>', '<blockquote><cite>$1 : </cite>$2</blockquote>', '<div style="text-align:left;">$1</div>', '<div style="text-align:right;">$1</div>', '<div style="text-align:center;">$1</div>', '<div style="text-align:justify;">$1</div>', '<ul>$1</ul>', '<ol>$1</ol>', '<li>$1</li>', '<img src="$1" alt="Image" />', '<a href="$1">$1</a>', '<a href="$1">$2</a>', '<a href="mailto:$1">$1</a>', '<a href="mailto:$1">$2</a>', '<br/>');
        $n = 1;
        while ($n > 0) {
            $aTxt = preg_replace($tag, $h, $aTxt, -1, $n);
        }
        $aTxt = preg_replace('/\[video\](.*?)\[\/video\]/is', '<video controls widht="300" height="200" preload="auto"><source src="$1"/><a href="$1">$1</a></video>', $aTxt);
        return preg_replace(array('/\[(.*?)\]/is', '/\[\/(.*?)\]/is'), '$1', $aTxt);
    }
}