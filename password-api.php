<?php
$password = $_GET['password'];
$action = isset($_GET['action']) ? $_GET['action'] : false;

// Get service status
if($action == 'getServiceStatus') {
  // Check the entropy of the password "test" to see if the service is online
  $passwordAnalyzer = new PasswordAnalyzer('test');
  if($passwordAnalyzer->entropy != null) {
    $response = array('status' => 'online');
  }
  else {
    $response = array('status' => 'offline');
  }

  echo json_encode($response);
  exit();
}
// Store the password
else if($action == 'storePassword') {
  // check if the request is for a password to store
  // AES encrypt with qLZmDn:Dto2]6)]tR9^wY<HJ8R@4W6W4js7zvZxA/tB#47(ofY
  // SELECT AES_DECRYPT(`password`, 'qLZmDn:Dto2]6)]tR9^wY<HJ8R@4W6W4js7zvZxA/tB#47(ofY') FROM stored_password
  // make sure request is SSL

  try {

    //file_put_contents('debug.txt', Indentify::indent(json_encode($_POST))); 
    $dbh = new PDO('mysql:host=localhost;dbname=security','security','jyph-wagh-gi-de');

    $stmt = $dbh->prepare('INSERT INTO stored_password (
          temporary,
          session,
          identifier,
          treatment,
          password,
          source,
          server,
          remote_address,
          referring_site,
          http_user_agent,
          manipulation_check,
          demographics,
          time_added
          )
        VALUES (
          :temporary,
          :session,
          :identifier,
          :treatment,
          AES_ENCRYPT(:password,"qLZmDn:Dto2]6)]tR9^wY<HJ8R@4W6W4js7zvZxA/tB#47(ofY"),
          :source,
          :server,
          :remote_address,
          :referring_site,
          :http_user_agent,
          :manipulation_check,
          :demographics,
          NOW()
          )');

    $stmt->bindParam(':temporary', $temporary);
    $stmt->bindParam(':session', $session);
    $stmt->bindParam(':identifier', $identifier);
    $stmt->bindParam(':treatment', $treatment);
    $stmt->bindParam(':password', $password);
    //$stmt->bindParam(':encryption_key', $encryption_key);
    $stmt->bindParam(':source', $source);
    $stmt->bindParam(':server', $server);
    $stmt->bindParam(':remote_address', $remote_address);
    $stmt->bindParam(':referring_site', $referring_site);
    $stmt->bindParam(':http_user_agent', $http_user_agent);
    $stmt->bindParam(':manipulation_check', $manipulation_check);
    $stmt->bindParam(':demographics', $demographics);

    $temporary = isset($_POST['temporary']) ? $_POST['temporary'] : 0;
    $session = isset($_POST['session']) ? $_POST['session'] : '';
    $identifier = isset($_POST['identifier']) ? $_POST['identifier'] : '';
    $treatment = isset($_POST['treatment']) ? $_POST['treatment'] : 'control';
    $password = isset($_POST['password']) ? $_POST['password'] : $_GET['password'];
    $encryption_key = 'qLZmDn:Dto2]6)]tR9^wY<HJ8R@4W6W4js7zvZxA/tB#47(ofY';
    $source = $_POST['source'] . ':ISYS-forensic2';


    $server = isset($_POST['server']) ? $_POST['server'] : json_encode($_SERVER);
    $server_array = isset($_POST['server']) ? json_decode($_POST['server'], true) : $_SERVER;
    $remote_address = $server_array['REMOTE_ADDR'];
    $referring_site = $server_array['HTTP_REFERER'];
    $http_user_agent = $server_array['HTTP_USER_AGENT'];

    $manipulation_check = isset($_POST['manipulation_check']) ? $_POST['manipulation_check'] : '';
    $demographics = isset($_POST['demographics']) ? $_POST['demographics'] : '';
    $stmt->execute() or file_put_contents('debug.txt', Indentify::indent('Error message: ' . json_encode($stmt->errorInfo())));

    //file_put_contents('debug.txt', Indentify::indent(var_dump($server_array)));
    $dbh = NULL;
  } catch (PDOException $e) {
    error_log("Error!: " . $e->getMessage() . "<br/>");
    die();
  }
  exit;
}
      // Get the password tip
else if($action == 'getTip' || $action === false) {
  //sleep(10);

  $passwordAnalyzer = new PasswordAnalyzer($password);

  // Set the tip type
  if(!isset($_GET['tipType'])) {
    $tipType = 'control';
  }
  else {
    $tipType = $_GET['tipType'];
  }

  // Set the response
  $response = array(
      'status' => 'success',
      'passwordStrengthText' => null,
      'passwordStrength' => null,
      'text' => null,
      );

  // Get the appropriate tip
  if($tipType == 'control') {
  }
  // Static text
  else if($tipType == 'staticText') {
    $response['text'] = $passwordAnalyzer->getStaticText();
  }
  // Dynamic text
  else if($tipType == 'dynamicText') {
    $response['text'] = $passwordAnalyzer->getDynamicText();        
  }
  // Strength meter
  else if($tipType == 'strengthMeter') {
    $response['passwordStrength'] = $passwordAnalyzer->strengthIndex;
    $response['passwordStrengthText'] = ucwords($passwordAnalyzer->strengthText);

    if($passwordAnalyzer->inKnownDataset) {
      $response['passwordStrengthText'] .= ' (known password)';
    }
  }
  // Dynamic text and strength meter
  else if($tipType == 'dynamicTextAndStrengthMeter') {
    $response['text'] = $passwordAnalyzer->getDynamicText();
    $response['passwordStrengthText'] = ucwords($passwordAnalyzer->strengthText);
    $response['passwordStrength'] = $passwordAnalyzer->strengthIndex;

    if($passwordAnalyzer->inKnownDataset) {
      $response['passwordStrengthText'] .= ' (known password)';
    }
  }
  else {
    echo json_encode($data);
    exit();
  }

  // Final output
  echo json_encode($response);
  exit();
}

function timeSinceString($unixTimeInSeconds) {
  //if(!is_int($unixTimeInSeconds)) {
  //    $unixTimeInSeconds = strtotime($unixTimeInSeconds);
  //}

  if($unixTimeInSeconds > 3150450000000000000) {
    $unixTimeInSeconds = 3150450000000000000;
  }


  // array of time period chunks
  $chunks = array(
      array(60 * 60 * 24 * 365 , 'year'),
      array(60 * 60 * 24 * 30 , 'month'),
      array(60 * 60 * 24 * 7, 'week'),
      array(60 * 60 * 24 , 'day'),
      array(60 * 60 , 'hour'),
      array(60 , 'minute'),
      array(1 , 'second'),
      );

  $today = time(); /* Current unix time  */
  //$since = $today - $unixTimeInSeconds;
  $since = $unixTimeInSeconds;

  // $j saves performing the count function each time around the loop
  for ($i = 0, $j = count($chunks); $i < $j; $i++) {

    $seconds = $chunks[$i][0];
    $name = $chunks[$i][1];

    // finding the biggest chunk (if the chunk fits, break)
    if (($count = floor($since / $seconds)) != 0) {
      // DEBUG print "<!-- It's $name -->\n";
      break;
    }
  }

  $response = ($count == 1) ? '1 '.$name : "$count {$name}s";

  if($name == 'year') {
    if($count > 999 && $count < 999999) {
      $response = round($count / 1000).' thousand years';
    }
    else if($count > 999999 && $count < 9999999) {
      $response = round($count / 1000000).' million years';
    }
    else if($count > 9999999 && $count < 99999999) {
      $response = round($count / 10000000).' billion years';
    }
    else if($count > 99999999) {
      $response = round($count / 100000000).' trillion years';
    }
  }

  if ($i + 1 < $j) {
    // now getting the second item
    $seconds2 = $chunks[$i + 1][0];
    $name2 = $chunks[$i + 1][1];

    // add second item if it's greater than 0
    if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
      //$print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
    }
  }
  return $response;
}

class PasswordAnalyzer {

  var $password;
  var $commonAppendages = array(
      '!',
      );
  var $specialCharacters = array('!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '+', '=', '_', '~', '`', '/', '\\', '{', '}', '[', ']', '\'', '"', ',', '.', '?');

  function __construct($password) {
    $this->password = $password;

    // Anaylze the password
    require_once('query-daemon.php');
    $response = queryPassword($password);

    $this->entropy = $response['all']['entropy'];
    $this->inKnownDataset = $response['optimal']['allMatched'];
    $this->partiallyInKnownDataset = (!empty($response['optimal']['numMatchedCharacters']) && !empty($response['optimal']['nonMatchedPart'])) ? 'Yes' : 'No';
    $this->attemptsPerSecond = 200;
    $this->numberOfCombinations = (float)$response['optimal']['hackNumCombinationsUpperBound'];
    $this->timeInSecondsToCrack = $this->numberOfCombinations / $this->attemptsPerSecond;
    $this->timeToCrack = timeSinceString($this->timeInSecondsToCrack);
    $this->passwordLength = $this->getLength($this->password);
    $this->hasLowercase = $this->containsLowerCaseCharacters($this->password) ? 'Yes' : 'No';
    $this->hasUppercase = $this->containsUpperCaseCharacters($this->password) ? 'Yes' : 'No';
    $this->hasNumbers = $this->containsNumbers($this->password) ? 'Yes' : 'No';
    $this->startsWithNumber = $this->startsWithNumber($this->password) === false ? 'No' : 'Yes';
    $this->endsWithNumber = $this->endsWithNumber($this->password) === false ? 'No' : 'Yes';
    $this->hasSpecialCharacters = $this->containsSpecialCharacters($this->password) ? 'Yes' : 'No';
    $this->hasSpace = $this->containsSpace($this->password) ? 'Yes' : 'No';
    $this->keySet = $this->getKeySet($this->password);
    $this->keySpace = $this->getKeySpace($this->password);
    $this->keySpacenNaturalLog = $this->getKeySpace($this->password, true);
    $this->toL33tSp34k = $this->plainToLeetSpeak($this->password);
    $this->strengthIndex = $this->getStrengthIndex();
    $this->strengthText = $this->getStrengthText();

    $this->strengthTextClass = ucwords($this->strengthText);
    $this->strengthTextClass[0] = strtolower($this->strengthTextClass[0]);
    $this->strengthTextClass = str_replace(' ', '', $this->strengthTextClass);
  }

  function getEnhancedPassword() {
    $enhancedPassword = $this->password;
    $enhancedPasswordArray = str_split($enhancedPassword);
    $charactersToAdd = array();

    // Add a lowercase character
    if($this->hasLowercase != 'Yes') {
      // If it has a letter, make the first letter you find uppercase and break
      $lowerCaseApplied = false;
      //foreach($enhancedPasswordArray as &$character) {
      //    if(ctype_upper($character)) {
      //        $character = strtolower($character);
      //        $lowerCaseApplied = true;
      //        break;
      //    }
      //}
      // If it doesn't have a lower case, add one
      if(!$lowerCaseApplied) {
        $charactersToAdd[] = 'a';
      }
    }

    if($this->hasUppercase != 'Yes') {
      // If it has a letter, make the first letter you find uppercase and break
      $upperCaseApplied = false;
      //foreach($enhancedPasswordArray as &$character) {
      //    if(ctype_lower($character)) {
      //        $character = strtoupper($character);
      //        $upperCaseApplied = true;
      //        break;
      //    }
      //}
      // If it doesn't have a lower case, add one
      if(!$upperCaseApplied) {
        $charactersToAdd[] = 'A';
      }
    }

    if($this->hasNumbers != 'Yes') {
      $charactersToAdd[] = rand()%9;
    }

    if($this->hasSpecialCharacters != 'Yes') {
      $charactersToAdd[] = '%';
    }

    if($this->hasSpace != 'Yes') {
      $charactersToAdd[] = ' ';
    }

    // Add the characters
    $enhancedPasswordArray = array_merge($enhancedPasswordArray, $charactersToAdd);
    $newPassword = implode($enhancedPasswordArray);

    while(strlen($newPassword) < 8) {
      $newPassword = $newPassword.'x';
    }

    if($this->partiallyInKnownDataset == 'Yes') {
      $newPassword = $newPassword.'1!0zz15 ';
    }

    return $newPassword;
  }

  function getStaticText() {
    $staticText = '
      <div class="susceptibility">
      <p>Hackers can guess common or simple passwords in a matter of <span class="timeToCrack veryInsecure">minutes or less</span>.</p>
      </div>
      <div class="severity">
      <p>
      Having your password guessed means a hacker would be able to access other accounts that use a similar password.
      </p>
      </div>
      <div class="selfEfficacy">
      <p>You can easily make your password more secure:</p>
      <ul class="static">
      <li class="checked">Avoid common passwords likely to be on a hacker password list</li>
      <li class="checked">Make it 8 characters long or more</li>
      <li class="checked">Add a lowercase character</li>
      <li class="checked">Add an uppercase character</li>
      <li class="checked">Add a number</li>
      <li class="checked">Add a special character (e.g., *, &, $)</li>
      <li class="checked">Add a space</li>
      <li class="speechBubble">Try using a passphrase like this:<br />"I like chocolate chip cookies."</li>
      </ul>
      </div>
      <div class="responseEfficacy">
      <p>Following these simple suggestions will make your password take <span class="timeToCrack verySecure">a thousand years</span> to guess.</p>
      </div>
      ';

    return $staticText;
  }

  function getDynamicText() {
    $tip = '';

    // Vulnerability
    //<div class="susceptibilityHeader"><p>'.ucwords($this->strengthText).'</p></div>
    $tip .= '
      <div class="susceptibility">
      <p>The password you entered is <span class="passwordStrength '.$this->strengthTextClass.'">'.$this->strengthText.'</span> and may take a hacker <span class="timeToCrack '.$this->strengthTextClass.'">'.$this->timeToCrack.'</span> to guess.</p>
      </div>
      ';

    // Recommendations
    $recommendations = array();
    $metRecommendations = 0;
    $unmetRecommendations = 0;

    if($this->inKnownDataset) {
      $recommendations[] = '<li class="unchecked">Modify your password &ndash; it is on a hacker password list</li>';
      $unmetRecommendations++;
    }

    if($this->partiallyInKnownDataset) {
      //$recommendations[] = '<li class="unchecked">Modify your password &ndash; it contains parts easily guessed by hackers</li>';
    }

    if($this->inWordNet) {
      $recommendations[] = '<li class="unchecked">Modify your password so it wont appear in a {type} dictionary</li>';
      $unmetRecommendations++;
    }

    if($this->passwordLength < 8) {
      $recommendations[] = '<li class="unchecked">Make it at least 8 characters long</li>';
      $unmetRecommendations++;
    }
    else {
      $recommendations[] = '<li class="checked">Make it at least 8 characters long</li>';
    }

    if($this->hasLowercase == 'No') {
      $recommendations[] = '<li class="unchecked">Add a lowercase character</li>';
      $unmetRecommendations++;
    }
    else {
      $recommendations[] = '<li class="checked">Add a lowercase character</li>';
    }

    if($this->hasUppercase == 'No') {
      $recommendations[] = '<li class="unchecked">Add an uppercase character</li>';
      $unmetRecommendations++;
    }
    else {
      $recommendations[] = '<li class="checked">Add an uppercase character</li>';
    }

    if($this->hasNumbers == 'No') {
      $recommendations[] = '<li class="unchecked">Add a number</li>';
      $unmetRecommendations++;
    }
    else {
      $recommendations[] = '<li class="checked">Add a number</li>';
    }

    if($this->hasSpecialCharacters == 'No') {
      $recommendations[] = '<li class="unchecked">Add a special character (e.g., *, &, $)</li>';
      $unmetRecommendations++;
    }
    else {
      $recommendations[] = '<li class="checked">Add a special character (e.g., *, &, $)</li>';
    }

    if($this->hasSpace == 'No') {
      $recommendations[] = '<li class="unchecked">Add a space</li>';
      $unmetRecommendations++;
    }
    else {
      $recommendations[] = '<li class="checked">Add a space</li>';
    }

    if($unmetRecommendations == 0 && strlen($this->password) < 20) {
      $recommendations[] = '<li class="speechBubble">Try using a passphrase like this:<br />"I like chocolate chip cookies."</li>';
    }

    // Significance
    $tip .= '
      <div class="severity">
      <p>Having your password guessed means a hacker would be able to access other accounts that use a similar password.</p>
      </div>
      ';

    $tip .= '
      <div class="selfEfficacy">
      <p>You can easily make your password more secure:</p>
      <ul>
      ';
    $recommendationCount = 0;
    foreach($recommendations as $recommendation) {
      $recommendationCount++;
      $tip .= $recommendation;
    }
    $tip .= '
      </ul>
      </div>
      ';

    // Enhance the password
    $optimizedPassword = $this->getEnhancedPassword();
    $optimizedPasswordAnalyzer = new PasswordAnalyzer($optimizedPassword);

    $tip .= '
      <div class="responseEfficacy">
      <p>Following these simple suggestions will make your password take <span class="passwordStrength '.$optimizedPasswordAnalyzer->strengthTextClass.'">'.$optimizedPasswordAnalyzer->timeToCrack.'</span> to guess.</p>
      </div>
      ';

    return $tip;
  }

  function getStrengthIndex() {
    $maxTimeInSecondsToCrack = 1 * 365 * 24 * 60 * 60;
    $strengthIndex = round(($this->timeInSecondsToCrack / $maxTimeInSecondsToCrack) * 100);
    if($strengthIndex > 100) {
      $strengthIndex = 100;
    }

    // If it is in a known dataset, kill the strength
    if($this->inKnownDataset) {
      $strengthIndex = 1;
    }

    // Give everything a little strength
    if($strengthIndex == 0) {
      $strengthIndex = 1;
    }

    $this->strengthIndex = $strengthIndex;
    return $this->strengthIndex;
  }

  function getStrengthText() {
    // Figure out password strength

    // Less than one hour
    if($this->strengthIndex < 20) {
      $text = 'very insecure';
    }
    //Less than one week (60*60*24*7) 604800
    else if($this->strengthIndex < 40) {
      $text = 'insecure';
    }
    //Less than one year (60*60*24*7*52)
    else if($this->strengthIndex < 60) {
      $text = 'fairly secure';
    }
    //Less than 100 years (60*60*24*7*10)
    else if($this->strengthIndex < 80) {
      $text = 'secure';
    }
    //More than 100 years
    else if($this->strengthIndex <= 100) {
      $text = 'very secure';
    }

    return $text;
  }

  function getLength($string) {
    return strlen($string);
  }

  function getKeySet($string) {
    // Find out character base by examining character set used
    $keyset = 0;

    if($this->containsNumbers($string)) {
      $keyset += 10;
    }

    if($this->containsSpace($string)) {
      $keyset += 1;
    }

    if($this->containsLowerCaseCharacters($string)) {
      $keyset += 26;
    }

    if($this->containsUpperCaseCharacters($string)) {
      $keyset += 26;
    }

    if($this->containsSpecialCharacters($string)) {
      $keyset += sizeof($this->specialCharacters); // How many special characters are there?
    }

    return $keyset;
  }

  function getKeySpace($string, $useNaturalLog = false) {
    if($useNaturalLog) {
      $keySpace = log(pow($this->getKeySet($string), $this->getLength($string)));
    }
    else {
      $keySpace = pow($this->getKeySet($string), $this->getLength($string));
    }

    return $keySpace;
  }

  function getEntropy($string) {
    if(strlen($string) <= 0)
      return 0.0;
    $symCount = array();
    foreach(str_split($string) as $character) {
      if(!in_array($character, $symCount))
        $symCount[$character] = 1;
      else
        $symCount[$character]++;
    }
    $entropy = 0.0;
    foreach($symCount as $character => $n) {
      $prob = $n / (float) strlen($string);
      $entropy += $prob * log($prob) / log(2);
    }
    if($entropy >= 0.0)
      return 0.0;
    else
      return -($entropy * strlen($string));
  }

  function bclog($X, $base=10, $decimalplace=12) {
    $integer_value = 0;
    while($X < 1) {
      $integer_value = $integer_value - 1;
      $X = bcmul($X, base);
    }
    while($X >= $base) {
      $integer_value = $integer_value + 1;
      $X = bcdiv($X, $base);
    }
    $decimal_fraction = 0.0;
    $partial = 1.0;
# Replace X with X to the 10th power
    $X = bcpow($X, 10);
    while($decimalplace > 0) {
      $partial = bcdiv($partial, 10);
      $digit = 0;
      while($X >= $base) {
        $digit = $digit + 1;
        $X = bcdiv($X, $base);
      }
      $decimal_fraction = bcadd($decimal_fraction, bcmul($digit, $partial));
# Replace X with X to the 10th power
      $X = bcpow($X, 10);
      $decimalplace = $decimalplace - 1;
    }
    return $integer_value + $decimal_fraction;
  }

  function containsSpace($string) {
    $containsSpace = false;
    for($i = 0; $i < strlen($string); $i++) {
      if($string[$i] == ' ') {
        $containsSpace = true;
      }
    }

    return $containsSpace;
  }

  function containsSpecialCharacters($string) {
    $containsSpecialCharacters = false;
    for($i = 0; $i < strlen($string); $i++) {
      if(in_array($string[$i], $this->specialCharacters)) {
        $containsSpecialCharacters = true;
      }
    }

    return $containsSpecialCharacters;
  }

  function containsLowerCaseCharacters($string) {
    $lowerCaseCharacters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');

    $containsLowerCaseCharacters = false;
    for($i = 0; $i < strlen($string); $i++) {
      if(in_array($string[$i], $lowerCaseCharacters)) {
        $containsLowerCaseCharacters = true;
      }
    }

    return $containsLowerCaseCharacters;
  }

  function containsUpperCaseCharacters($string) {
    $upperCaseCharacters = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

    $containsUpperCaseCharacters = false;
    for($i = 0; $i < strlen($string); $i++) {
      if(in_array($string[$i], $upperCaseCharacters)) {
        $containsUpperCaseCharacters = true;
      }
    }

    return $containsUpperCaseCharacters;
  }

  function containsNumbers($string) {
    $numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');

    $containsNumbers = false;
    for($i = 0; $i < strlen($string); $i++) {
      if(in_array($string[$i], $numbers)) {
        $containsNumbers = true;
      }
    }

    return $containsNumbers;
  }

  function endsWithNumber($string) {
    if(!is_int($string[strlen($string) - 1])) {
      return false;
    }

    for($i = strlen($string) -1, $int = ''; $i >= 0; $i--) {
      if(is_numeric($string[$i]))
        $int = $string[$i].$int;
      else
        break;
    }

    return $int;
  }

  function startsWithNumber($string) {
    if(!is_int($string[0])) {
      return false;
    }

    $length = strlen($string);
    for($i = 0, $int = ''; $i < $length; $i++) {
      if(is_numeric($string[$i]))
        $int .= $string[$i];
      else
        break;
    }

    return $int;
  }

  function endsWithSymbol($string) {

  }

  function startsWithSymbol($string) {

  }

  function plainToLeetSpeak($string) {
    $leetify = new Leetify();
    return $leetify->encode($string);
  }

  function leetSpeakToPlain($string) {

  }

  function startsWithCommonAppendage($string) {
    $startsWithCommonAppendage = false;
    foreach($commonAppendages as $commonAppendage) {
      if(String::startsWith($string, $commonAppendage)) {
        $startsWithCommonAppendage = true;
      }
    }

    return $startsWithCommonAppendage;
  }

  function endsWithCommonAppendage($string) {
    $endsWithCommonAppendages = false;
    foreach($commonAppendages as $commonAppendage) {
      if(String::startsWith($string, $commonAppendage)) {
        $endsWithCommonAppendages = true;
      }
    }

    return $endsWithCommonAppendages;
  }

}

class Leetify
{
  private $english = array("a", "e", "s", "S", "A", "o", "O", "t", "l", "ph", "y", "H", "W", "M", "D", "V", "x");
  private $leet = array("4", "3", "z", "Z", "4", "0", "0", "+", "1", "f", "j", "|-|", "\\/\\/", "|\\/|", "|)", "\\/", "><");
  function encode($string)
  {
    $result = '';
    for ($i = 0; $i < strlen($string); $i++)
    {
      $char = $string[$i];

      if (false !== ($pos = array_search($char, $this->english)))
      {
        $char = $this->leet[$pos]; //Change the char to l33t.
      }
      $result .= $char;
    }
    return $result;
  }

  function decode($string)
  {
    //just reverse the above.
  }
}

class Indentify {

  /**
   * Indents a flat JSON string to make it more human-readable.
   *
   * @param string $json The original JSON string to process.
   *
   * @return string Indented version of the original JSON string.
   */
  public static function indent($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

      // Grab the next character in the string.
      $char = substr($json, $i, 1);

      // Are we inside a quoted string?
      if ($char == '"' && $prevChar != '\\') {
        $outOfQuotes = !$outOfQuotes;

        // If this character is the end of an element, 
        // output a new line and indent the next line.
      } else if(($char == '}' || $char == ']') && $outOfQuotes) {
        $result .= $newLine;
        $pos --;
        for ($j=0; $j<$pos; $j++) {
          $result .= $indentStr;
        }
      }

      // Add the character to the result string.
      $result .= $char;

      // If the last character was the beginning of an element, 
      // output a new line and indent the next line.
      if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
        $result .= $newLine;
        if ($char == '{' || $char == '[') {
          $pos ++;
        }

        for ($j = 0; $j < $pos; $j++) {
          $result .= $indentStr;
        }
      }

      $prevChar = $char;
    }

    return $result;
  }
}
?>
