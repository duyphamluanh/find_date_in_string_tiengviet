<?php
function vn_to_str ($str){
    $unicode = array(
    'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
    'd'=>'đ',
    'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
    'i'=>'í|ì|ỉ|ĩ|ị',
    'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
    'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
    'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
    'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
    'D'=>'Đ',
    'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
    'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
    'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
    'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
    'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
    );
     
    foreach($unicode as $nonUnicode=>$uni){
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }
    // $str = str_replace(' ','_',$str);
     
    return $str;
}

function find_date_in_string($string) {
	$shortenize = function($string) {
    return substr($string, 0, 3);
  };

  // Define month name:
  $month_names = array(
    "january","february","march","april","may","june","july","august","september","october","november","december");
  $short_month_names = array_map($shortenize, $month_names);

  // Define day name
  $day_names = array(
    "monday","tuesday","wednesday","thursday","friday","saturday","sunday");
  $short_day_names = array_map($shortenize, $day_names );

  // Define ordinal number
  $ordinal_number = ['st', 'nd', 'rd', 'th', '<sup>st<\/sup>', '<sup>nd<\/sup>', '<sup>rd<\/sup>', '<sup>th<\/sup>'];

  $day = "";
  $month = "";
  $year = "";

  // Match dates: 01/01/2012 or 30-12-11 or 1 2 1985
  preg_match_all( '/([0-3]?[0-9])[\.\-\/ ]+([0-1]?[0-9])[\.\-\/ ]+([0-9]{4})/', $string, $matches);

  $result = array();
  foreach($matches[0] as $key => $match) {
    $result[] = array(
      "day" => $matches[1][$key],
      "month" => $matches[2][$key], 
      "year" => $matches[3][$key]
    );
  }
  unset($matches);
  // print_r($result);
  // return $result;

  // Match dates: Sunday 1st March 2015; Sunday, 1 March 2015; Sun 1 Mar 2015; Sun-1-March-2015; Saturday, 4<sup>th</sup> December, 2021
  $stringforcase2 = strtolower($string);
  preg_match_all('/(?:(?:' . implode( '|', $day_names ) . '|' . implode( '|', $short_day_names ) . ')[ ,\-_\/]*)?([0-9]?[0-9])[ ,\-_\/]*(?:' . implode( '|', $ordinal_number ) . ')?[ ,\-_\/]*(' . implode( '|', $month_names ) . '|' . implode( '|', $short_month_names ) . ')[\r\n|\r|\n]*[ ,\-_\/]*[\r\n|\r|\n]*([0-9]{4})/i', $stringforcase2, $matches );
  if ($matches) {
    foreach($matches[0] as $key => $match) {
      if ($matches[1][$key]) {
        $day = $matches[1][$key];
      }
      if ($matches[2]) {
        $month = array_search($matches[2][$key],  $short_month_names);
        if (!$month) {
          $month = array_search($matches[2][$key],  $month_names);
        }
        $month = $month + 1;
      }

      if ($matches[3][$key]){
        $year = $matches[3][$key];
      }
      $result[] = array(
        "day" => $day,
        "month" => $month, 
        "year" => $year
      );
    }
  }

  // Match dates: March 1st 2015; March 1 2015; March-1st-2015
  $stringforcase3 = strtolower($string);
  preg_match_all('/(' . implode('|', $month_names) . '|' . implode('|', $short_month_names) . ')[ ,\-_\/]*([0-9]?[0-9])[ ,\-_\/]*(?:' . implode('|', $ordinal_number) . ')?[\r\n|\r|\n]*[ ,\-_\/]*[\r\n|\r|\n]*([0-9]{4})/i', $stringforcase3, $matches);
  if ($matches) {
    foreach ($matches[0] as $key => $match) {
      if ($matches[1][$key]) {
        $month = array_search($matches[1][$key],  $short_month_names);
        if (!$month) {
          $month = array_search($matches[1][$key],  $month_names);
        }
        $month = $month + 1;
      }

      if ($matches[2][$key]) {
        $day = $matches[2][$key];
      }
      if ($matches[3][$key]) {
        $year = $matches[3][$key];
      }
      $result[] = array(
        "day" => $day,
        "month" => $month, 
        "year" => $year
      );
    }
  }

  // Match dates: ngày 16 tháng 01 năm 2021
  $stringforcase3 = vn_to_str(strtolower($string));
  preg_match_all('/(ngay)*[ ,\-_\/]*([0-3]*?[0-9])[ ,\-_\/]*(thang)[ ,\-_\/]+([0-1]*?[0-9])[ ,\-_\/]*(nam)[\r\n|\r|\n]*[ ,\-_\/]*[\r\n|\r|\n]*([0-9]{4})/i', $stringforcase3, $matches);
  if ($matches) {
    foreach ($matches[0] as $key => $match) {
      if ($matches[2][$key]) {
        $day = $matches[2][$key];
      }
      if ($matches[4][$key]) {
        $month = $matches[4][$key];
      }
      if ($matches[6][$key]) {
        $year = $matches[6][$key];
      }
      $result[] = array(
        "day" => $day,
        "month" => $month, 
        "year" => $year
      );
    }
  }

  print_r($result);
  if(!count($result) == 0) {
    return false;
  } 
  return $result;

  // // Match month name:
  // if ( empty( $month ) ) {
  //   preg_match( '/(' . implode( '|', $month_names ) . ')/i', $string, $matches_month_word );
  //   if ( $matches_month_word && $matches_month_word[1] )
  //     $month = array_search( strtolower( $matches_month_word[1] ),  $month_names );

  //   // Match short month names
  //   if ( empty( $month ) ) {
  //     preg_match( '/(' . implode( '|', $short_month_names ) . ')/i', $string, $matches_month_word );
  //     if ( $matches_month_word && $matches_month_word[1] )
  //       $month = array_search( strtolower( $matches_month_word[1] ),  $short_month_names );
  //   }

  //   $month = $month + 1;
  // }

  // // Match 5th 1st day:
  // if ( empty( $day ) ) {
  //   preg_match( '/([0-9]?[0-9])(' . implode( '|', $ordinal_number ) . ')/', $string, $matches_day );
  //   if ( $matches_day && $matches_day[1] )
  //     $day = $matches_day[1];
  // }

  // // Match Year if not already setted:
  // if ( empty( $year ) ) {
  //   preg_match( '/[0-9]{4}/', $string, $matches_year );
  //   if ( $matches_year && $matches_year[0] )
  //     $year = $matches_year[0];
  // }
  // if ( ! empty ( $day ) && ! empty ( $month ) && empty( $year ) ) {
  //   preg_match( '/[0-9]{2}/', $string, $matches_year );
  //   if ( $matches_year && $matches_year[0] )
  //     $year = $matches_year[0];
  // }

  // // Day leading 0
  // if ( 1 == strlen( $day ) )
  //   $day = '0' . $day;

  // // Month leading 0
  // if ( 1 == strlen( $month ) )
  //   $month = '0' . $month;

  // // Check year:
  // if ( 2 == strlen( $year ) && $year > 20 )
  //   $year = '19' . $year;
  // else if ( 2 == strlen( $year ) && $year < 20 )
  //   $year = '20' . $year;

  // $date = array(
  //   'year'  => $year,
  //   'month' => $month,
  //   'day'   => $day
  // );

  // // Return false if nothing found:
  // if ( empty( $year ) && empty( $month ) && empty( $day ) )
  //   return false;
  // else
  //   return $date;
}

$teststring = "<p>2. Thời gian: 15h30 – 16h30 ngày 16/05/2021 1/2/2055 July 2st 2025;"
."ngày 6 tháng 1 năm 2021"
// ."</p> Sunday 1st March 2015; Monday, 20 March 2022;"
// ." Sun 19 Mar 2022; Saturday-18-March-2022"
;
$teststring1 = '<p><p>Discuss each
interpreting method (Consecutive interpreting - Simultaneous/Cabin interpreting
- Sight interpreting) would be appropriate for each case. (See documentation on
LMS). Each student will make an mp3 file recording his/her answer and upload it
onto the LMS system not later than 23:00 hours on Saturday, 4<sup>th</sup> December,
2021 to be graded. The mp3 file name is set according to the example: Tran Thi
Ngoc Anh - PD 2111 - VC 1.</p>

<p>Thảo luận mỗi phương thức
phiên dịch [Dịch đuổi hay dịch kế
tiếp (Consecutive interpreting)<b> - </b>Dịch đồng thời/ca bin (Simultaneous/Cabin
interpreting) - Dịch nhìn văn bản (sight interpreting)]
sẽ phù hợp cho mỗi trường hợp nào. (Xem tài liệu trên LMS). Mỗi sinh viên sẽ
làm một file mp3 ghi âm phần trả lời của mình và upload lên hệ thống LMS không
trễ hơn 23g00 ngày thứ bảy 05.11.2021 để được chấm điểm. Tên file mp3 được đặt
theo thí dụ: ‘Tran Thi Ngoc Anh - PD 2111 - VC 1’. </p><br></p>
';

find_date_in_string(
  $teststring1
);
