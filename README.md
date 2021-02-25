# LE-AI
linguistic expansion by LE-AI expert.ai simplyfied API

To use the LE-AI the light expert AI simplified API you need to register as a developer on the expert.ai site

In this way you get the username, that is your email, and the password.
These two pieces of information are the only ones needed to be able to use the simplified API.

For now LE-AI is in PHP code but I hope others will join together to create their equivalent in other languages. 

To use LE-AI read the documentation inside the code.

The release of the LE-AI is under the MIT license.

#FEATURES AND FUNCTIONALITY
##Of all the possibilities provided by the expert.ai API, the most significant have been isolated for simplification 
##1) generate the authorization token only if necessary and automatically, always using the latest available and active 
##2) this is the list of variables used thanks to the simplified API, minimum 8, maximum 11:
###	$eai_language			// the used language
###	$eai_content			// the content text
###	$eai_mainSentences		// the main sentence (if exist more sentences)
###	$eai_knowledge			// the type of knowledge
###	$eai_mainLemmas		// the main lemma
###	$eai_mainLemmas1		// the second main lemma (if exist)
###	$eai_mainPhrases		// the main phrase
###	$eai_mainSyncons		// the main syncon
###	$eai_mainSyncons1		// the second main syncon (if exist)
###	$eai_sentiment			// the sentiment (float number from 0 to 100)
###	$eai_topic				// the topic

RESERVED INFORMATION Modify the content of variables:
$eai_usrn=	'CHANGE_WITH_YOUR_EMAIL';				// your developer username is email
$eai_pswd=	'CHANGE_WITH_YOUR_PASSWORD';			// your developer password

SETTING Modify the content of variables for production:
$devmod=	true;									// true for develop mode   false for production mode

TEXT TO ELABORATE
$toanalyze=	'what is the best idea to make more money?';	// phrase to analyze
