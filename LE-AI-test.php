<?php
// ______________________________________________________
// Copyright (c) 2021 Salvatore Mocciaro
// 
// under MIT license
// Permission is hereby granted, free of charge, to any person obtaining a
// copy of this software and associated documentation files (the "Software"),
// to deal in the Software without restriction, including without limitation the
// rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
// sell copies of the Software, and to permit persons to whom the Software
// is furnished to do so, subject to the following conditions:
// 
// The above copyright notice and this permission notice shall be included
// in all copies or substantial portions of the Software.
// 
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
// EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
// OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
// NONINFRINGEMENT.
// IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
// FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
// OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
// CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
// ______________________________________________________
//
// LE-AI the light expert.ai simplified API
// PHP 7.4 requested
// version 0.01
// ______________________________________________________

// HOW TO USE THE API
//	To use the simplified API you need to register as a developer on the expert.ai site
//	In this way you get the username, that is your email, and the password.
//	These two pieces of information are the only ones needed to be able to use the simplified API.
//	Modify the line signed with ***

//	modify the next variable values for test the code:
//	$eai_usrn		with your developer username, that is your email
//	$eai_pswd	with your developer password
//	$filenameT	the file name to preserve the last useful token
//	$devmod		true for develop mode   false for production mode
//	$toanalyze	phrase to analyze

// ______________________________________________________
// DEVELOPMENT OR PRODUCTION MODE
$devmod=		true;					// true for develop mode   false for production mode ***

if($devmod){
	// development
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL | E_STRICT);
}else{
	// production
	ini_set('display_errors', '0');
	ini_set('display_startup_errors', '0');
	error_reporting(E_ALL & ~E_DEPRECATED);
}
// ______________________________________________________

// ______________________________________________________
// START FUNCTION DEFINITION FOR LE-AI

// GENERATION OF THE AUTHORIZATION TOKEN FOR API
function LEAI_expertai_get_token(string $eai_usrn,	string $eai_pswd) : array{
	// $eai_usrn is your developer username (email)
	// $eai_pswd is your developer password
	$ch = curl_init();
	//curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);	// skip certificate check
	//curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, true);	// Check whether the SSL encryption algorithm exists from the certificate
	curl_setopt($ch, CURLOPT_URL, 'https://developer.expert.ai/oauth2/token');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"username\": \"$eai_usrn\", \"password\": \"$eai_pswd\"}");

	$headers=	[];
	$headers[]=	'Content-Type: application/json; charset=utf-8';
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$eai_token=	curl_exec($ch);						// return the authorization token

	if(curl_errno($ch)){
		$eai_error=	curl_error($ch);
	}else{
		$eai_error=	'';
	}

	curl_close($ch);

	return [$eai_token, $eai_error];
}

// ACCESS TO API FOR FULL ANALYSIS OF TEXT
function LEAI_expertai_full_analysis(string $toanalyze, string $eai_token) : array{
	// $toanalize is the phrase to analyze
	// $eai_token is the autorization token
	$ch = curl_init();
	//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);	// skip certificate check
	//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);		// Check whether the SSL encryption algorithm exists from the certificate
	curl_setopt($ch, CURLOPT_URL, 'https://nlapi.expert.ai/v2/analyze/standard/en');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"document\": {\"text\": \"$toanalyze\"}}");

	$headers=	[];
	$headers[]=	"Authorization: Bearer $eai_token";
	$headers[]=	'Content-Type: application/json; charset=utf-8';

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$eai_resp=	curl_exec($ch);

	if(curl_errno($ch)){
		$eai_error=	curl_error($ch);
	}else{
		$eai_error=	'';
	}

	curl_close($ch);

	$eai_json=	json_decode($eai_resp, true);		// transform json string to json array PHP format

	return [$eai_json, $eai_error];
}

// END FUNCTION DEFINITION
// ______________________________________________________




// ______________________________________________________
// CODE IN THE MAIN PROGRAM

// RESERVED INFORMATION
$eai_usrn=		'************';						// your developer username is email ***
$eai_pswd=		'**********************';				// your developer password ***

// TEXT TO ELABORATE
$toanalyze=		'what is the best idea to make more money?';	// phrase to analyze ***

// LOGICAL VARIABLE SETTING
$newT=			false;				// new token request is setting to false
$eai_error=		'';					// initial value of error
$eai_token=		'';					// initial value of token

// NOTE FOR SECURITY OF AUTHORIZATION TOKEN: block the access to the file using apache .htaccess <Files> or similar
$filenameT=		'eai_token_file_name';	// the file name to preserve the last useful token ***

// LIST OF   $eai_*   variable
$eai_language=			'';				// the language of the content text
$eai_content=			'';				// the content text
$eai_mainSentences=	'';				// the main sentence of the text
$eai_knowledge=		'';				// the type of knowledge of the text
$eai_mainLemmas=		'';				// the main lemma of the text
$eai_mainLemmas1=		'';				// the second main lemma of the text (if exist)
$eai_mainPhrases=		'';				// the main phrase of the text
$eai_mainSyncons=		'';				// the main syncon of the text
$eai_mainSyncons1=		'';				// the second main syncon of the text (if exist)
$eai_sentiment=		'';				// the sentiment of the text
$eai_topic=			'';				// the topic of the text

// TEST IF VALID TOKEN EXIST
if($eai_token==''){						// if token is void
	if(file_exists($filenameT)){									// if exist previus saved token in file
		$eai_token=	file_get_contents($filenameT);				// get the existing token
	}else{												// if not exist previus saved token
		[$eai_token, $eai_error]=	LEAI_expertai_get_token($eai_usrn,	$eai_pswd);	// token generation
		if($eai_error){					// on error
			if($devmod){
				echo " <br>000 get token error calling API expert.ai: $eai_error  <br>";	// return the error
			}
		}else{
			$newT=	true;									// sign that new token need to saved
		}
	}
}

if($eai_error){		// on error
	if($devmod){
		// assign token manually generated from expert.ai page: 
		$eai_token=	'*******************************';	// ***
		//
		// example curl inline command where the word    'token'    need to change with the right token
		// curl -X POST https://nlapi.expert.ai/v2/analyze/standard/en  -H "Authorization: Bearer token" -H "Content-Type: application/json; charset=utf-8" -d "{\"document\": {\"text\": \"what is the best idea to make more money?\"}}"
		//
		$newT=		true;									// sign that new token need to saved
	}
}

// IF EVERYTHING IS OK, ASSIGN VARIABLES SET   $eai_*   FOR USE INSIDE THE PROGRAM
if($eai_token){													// if token is not empty
	if($toanalyze){												// if text is not empty
		[$eai_json, $eai_error]=	LEAI_expertai_full_analysis($toanalyze, $eai_token);			// analyze the text
		if($eai_error){											// on error try to generate a new token
			if($devmod){
				echo " <br>001 error calling API expert.ai: $eai_error  <br>";				// return the error
			}
			[$eai_token, $eai_error]=	LEAI_expertai_get_token($eai_usrn,	$eai_pswd);		// token generation
			if($eai_error){														// on error
				if($devmod){
					echo " <br>002 error calling API expert.ai: $eai_error  <br>";			// return the error
				}
			}else{
				[$eai_json, $eai_error]=	LEAI_expertai_full_analysis($toanalyze, $eai_token);	// analyze the text
				if($eai_error){													// on error
					if($devmod){
						echo " <br>003 error calling API expert.ai: $eai_error  <br>";		// return the error
					}
				}else{
					// the following variables can be used according to the program logic 
					$eai_language=			($eai_json["data"]["language"])??				'';		// example: "en"

					$eai_content=			($eai_json["data"]["content"])??					'';		// example: "what is the best idea to make more money?"

					$eai_mainSentences=	($eai_json["data"]["mainSentences"][0]["value"])??	'';		// example: "what is the best idea to make more money?"

					$eai_knowledge=		($eai_json["data"]["knowledge"][0]["label"])??		'';		// example: "knowledge.form_of_thought"
					$eai_knowledge=		preg_replace('/^knowledge\./iu','',$eai_knowledge);			// example: "form_of_thought"

					$eai_mainLemmas=		($eai_json["data"]["mainLemmas"][0]["value"])??	'';		// example: "money"

					$eai_mainLemmas1=		($eai_json["data"]["mainLemmas"][1]["value"])??	'';		// example: "idea"

					$eai_mainPhrases=		($eai_json["data"]["mainPhrases"][0]["value"])??		'';		// example: "best idea"

					$eai_mainSyncons=		($eai_json["data"]["mainSyncons"][0]["lemma"])??	'';		// example: "money"

					$eai_mainSyncons1=		($eai_json["data"]["mainSyncons"][1]["lemma"])??	'';		// example: "thought"

					$eai_sentiment=		($eai_json["data"]["sentiment"]["overall"])??		'';		// example:  13.1

					$eai_topic=			($eai_json["data"]["topics"][0]["label"])??			'';		// example:  "the economy"

					if($newT){													// if new token are generated
						// NOTE: only on success we can save the new token for future uses 
						file_put_contents($filenameT,	$eai_token);						// save in file the new token
					}
				}
			}
		}
	}else{
		if($devmod){
			echo "<br>004 error: text to analyze is void <br>";
		}
	}
}else{
	if($devmod){
		echo "<br>005 error: token API expert.ai is void <br>";
	}
}

// TEST FOR THE RIGHT RESULT IF DEVELOPER MODE IS ON
if($devmod){
	echo '<br>';
	echo 'language= '; var_dump($eai_language); echo '<br>';
	echo 'content= '; var_dump($eai_content); echo '<br>';
	echo 'mainSentences= '; var_dump($eai_mainSentences); echo '<br>';
	echo 'knowledge= '; var_dump($eai_knowledge); echo '<br>';
	echo 'mainLemmas= '; var_dump($eai_mainLemmas); echo '<br>';
	echo 'mainLemmas1= '; var_dump($eai_mainLemmas1); echo '<br>';
	echo 'mainPhrases= '; var_dump($eai_mainPhrases); echo '<br>';
	echo 'mainSyncons= '; var_dump($eai_mainSyncons); echo '<br>';
	echo 'mainSyncons1= '; var_dump($eai_mainSyncons1); echo '<br>';
	echo 'sentiment= '; var_dump($eai_sentiment); echo '<br>';
	echo 'topic= '; var_dump($eai_topic); echo '<br>';
}

/* RESULT OF TEST
language= string(2) "en"
content= string(41) "what is the best idea to make more money?"
mainSentences= string(41) "what is the best idea to make more money?"
knowledge= string(15) "form_of_thought"
mainLemmas= string(5) "money"
mainLemmas1= string(4) "idea"
mainPhrases= string(9) "best idea"
mainSyncons= string(5) "money"
mainSyncons1= string(7) "thought"
sentiment= float(13.1)
topic= string(11) "the economy"
*/
