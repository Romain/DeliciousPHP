<?php

class Delicious {

	// Cette classe a pour but de faciliter l'usage de l'API delicious
	// 4 grandes rubriques de méthodes co-existent
	// 		- celles concernant les updates
	// 		- celles concernant les posts
	// 		- celles concernant les tags
	// 		- celles concernant les tag bundles
	// L'idée est de fournir les fonctions qui vont faire appel à ces mé�thodes.
	// L'API Delicious renvoie soit un code d'erreur, soit dans le cas d'un succ�s, du contenu au format xml.
	// Ce contenu devra etre �crit dans un fichier xml
	// Il ne reste plus qu'� parser le contenu de ce fichier pour afficher les �l�ments souhait�s.
	//
	//
	// Voici quelques consignes donn�es par Delicious.
	// Vous devrez en tenir compte dans les d�veloppement que vous r�aliserez en utilisant cette librairie.
	//		1. Please let us know if you are going to release software that uses this publicly,
	//		so that we can at least have a heads-up and hopefully test things out beforehand.
	//		2. Please wait AT LEAST ONE SECOND between queries, or you are likely to get automatically throttled.
	//		If you are releasing a library to access the API, you MUST do this.
	//		3. Please watch for 500 or 999 errors and back-off appropriately. It means that you have been throttled.
	//		4. Please set your User-Agent to something identifiable.
	//		The default identifiers like "Java/1.4.3" or "lwp-perl" etc tend to get banned from time to time.
	//		5. If you are releasing software or a service for other people to use,
	//		your software or service MUST NOT add any links without a user's explicit direction. 
	//		Likewise, you MUST NOT modify any urls except under the user's explicit direction.
	
	

	public $username = NULL;
	public $password = NULL;
	public $api = "api.del.icio.us/v1";
	public $retour = NULL;
	
	// Variables used by the methods
	// Be careful, some of them are used by different methods, don't forget to clear them or to unset your object before reuse them.
	public $tag = NULL;
	public $dt = NULL; // is a date
	public $url = NULL;
	public $hashes = NULL;
	public $meta = NULL;
	public $description = NULL;
	public $extended = NULL;
	public $replace = NULL;
	public $shared = NULL;
	public $nb = NULL;
	public $start = NULL;
	public $results = NULL;
	public $fromdt = NULL;
	public $todt = NULL;
	public $bundle = NULL;
	
/********************************************************************************************************************************************************/
	
	// Constructeur de la classe file
	// Cette fonction est appel�e au moment de l'initialisation de l'objet
	// et re�oit les arguments transmis � la classe lors de l'instanciation de l'objet.
	function __construct() {
		$this->username = NULL;
		$this->password = NULL;
	 	$this->api = "api.del.icio.us/v1";
		$this->retour = NULL;
		
		$this->tag = NULL;
		$this->dt = NULL;
		$this->url = NULL;
		$this->hashes = NULL;
		$this->meta = NULL;
		$this->description = NULL;
		$this->extended = NULL;
		$this->replace = NULL;
		$this->shared = NULL;
		$this->nb = NULL;
		$this->start = NULL;
		$this->results = NULL;
		$this->fromdt = NULL;
		$this->todt = NULL;
		$this->bundle = NULL;
		
		for($i=0;$i<func_num_args();$i++)
			$this->args[$i] = func_get_arg($i);
	}
	
	
/********************************************************************************************************************************************************/

	// Call to the API
	
	function connect($method){
		$apicall = "https://".$this->username.":".$this->password."@".$this->api.$method;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$apicall);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->username);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$xml = curl_exec ($ch);
		curl_close ($ch);
		
		return $xml;
	}
	
	
/********************************************************************************************************************************************************/

	// Update
	// Returns the last update time for the user, as well as the number of new items in the user's inbox since it was last visited.
	function update() {
		$method = "/posts/update";
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	
/********************************************************************************************************************************************************/

	// Posts
	
	// Add
	// Add a post to Delicious
	// Arguments :
	//		(required) URL : url of the item
	//		(required) description : the description of the item
	//		(optional) extended : notes for the item
	//		(optional) tag : tags for the item (space delimited)
	//		(optional) dt : datestamp of the item (format "CCYY-MM-DDThh:mm:ssZ").
	//						Requires a LITERAL "T" and "Z" like in ISO8601 at http://www.cl.cam.ac.uk/~mgk25/iso-time.html for example: "1984-09-01T14:21:31Z"
	//		(optional) replace : don't replace post if given url has already been posted
	//			If used, value MUST be "no"
	//		(optional) shared : make the item private
	//			If used, value MUST be "no"
	// Example responses :
	//		Success : <result code="done" />
	//		Failure : <result code="something went wrong" />
	function addPost() {
		$method = "/posts/add?";
		
		if($this->tag != NULL)
			$method .= "tag=".$this->tag."&";
			
		if($this->dt != NULL)
			$method .= "date=".$this->dt."&";
			
		if($this->url != NULL)
			$method .= "url=".$this->url."&";
			
		if($this->description != NULL)
			$method .= "description=".$this->description."&";
			
		if($this->extended != NULL)
			$method .= "extended=".$this->extended."&";
			
		if($this->replace != NULL)
			$method .= "replace=".$this->replace."&";
			
		if($this->shared != NULL)
			$method .= "shared=".$this->shared;
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	// Delete
	// Delete a post from Delicious
	// Arguments
	// 		(required) URL : The url of the item
	// To define the URL argument, just give a value to $myDelicious->url.
	// Example response : <result code="done" />
	function deletePost() {
		$method = "/posts/delete?";
			
		// If you want to filter by url
		if($this->url != NULL)
			$method .= "url=".$this->url;
		else
			return false;
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	// Get
	// Returns one or more posts on a single day matching the arguments. If no date or url is given, most recent date will be used.
	// Arguments
	//		Tag (optional) Filter by this tag. A list of tags can be gathered, using + between each tag.
	//			Ex. : $myDelicious->tag = "tag1+tag2+tag3";
	//		Date (optional) Filter by this date, defaults to the most recent date on which bookmarks were saved.
	//			Ex. : &dt={CCYY-MM-DDThh:mm:ssZ}
	//		URL (optional) Fetch a bookmark for this URL, regardless of date. Note: Be sure to URL-encode the argument value.
	//		Hashes (optional) Fetch multiple bookmarks by one or more URL MD5s regardless of date, separated by URL-encoded spaces (ie. '+').
	//		Meta (optional) Include change detection signatures on each item in a 'meta' attribute. Clients wishing to maintain a synchronized local store of bookmarks should retain the value of this attribute - its value will change when any significant field of the bookmark changes.
	// If you don't want to use one or all of this arguments, DO NOT define variables $this->tag, $this->date, $this->url, $this->hashes, $this->meta.
	// Returns xml
	function getPost() {
		$method = "/posts/get?";
		
		// If you want to filter by tag(s)
		if($this->tag != NULL)
			$method .= "tag=".$this->tag."&";
			
		// If you want to filter by date
		if($this->dt != NULL)
			$method .= "date=".$this->dt."&";
			
		// If you want to filter by url
		if($this->url != NULL)
			$method .= "url=".$this->url."&";
			
		// If you want to filter by hashes
		if($this->hashes != NULL)
			$method .= "hashes=".$this->hashes."&";
			
		// If you want to filter by meta
		if($this->meta != NULL)
			$method .= "meta=".$this->meta;
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	// Recent
	// Returns a list of the most recent posts, filtered by argument. Maximum 100.
	// Arguments
	//		(optional) tag : filter by this tag
	//		(optional) count : number of items to retrieve (Default : 15, Max : 100, Min : 1)
	// Returns xml
	function recentPost() {
		$method = "/posts/recent?";
		
		// If you want to filter by tag(s)
		if($this->tag != NULL)
			$method .= "tag=".$this->tag."&";
			
		// If you want to filter by number
		if($this->nb != NULL)
			$method .= "count=".$this->nb;
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	// Dates
	// Returns a list of dates with the number of posts at each date.
	// Arguments
	//		(optional) tag : filter by this tag
	// Returns xml
	function datesPost() {
		$method = "/posts/recent?";
		
		// If you want to filter by tag(s)
		if($this->tag != NULL)
			$method .= "tag=".$this->tag;
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	// All
	// Returns all posts. Please use sparingly. Call the update function to see if you need to fetch this at all.
	// Arguments
	//		(optional) tag : Filter by this tag.
	//		(optional) start : Start returning posts this many results into the set.
	//		(optional) results : Return this many results.
	//		(optional) fromdt : Filter for posts on this date or later
	//			Type of value : &fromdt={CCYY-MM-DDThh:mm:ssZ}
	//		(optional) todt : Filter for posts on this date or earlier
	//			Type of value : &todt={CCYY-MM-DDThh:mm:ssZ}
	//		(optional) meta : Include change detection signatures on each item in a 'meta' attribute.
	//		Clients wishing to maintain a synchronized local store of bookmarks should retain the value of this attribute
	///		- its value will change when any significant field of the bookmark changes.
	function allPost() {
		$method = "/posts/all?";
		
		if($this->tag != NULL)
			$method .= "tag=".$this->tag."&";
			
		if($this->start != NULL)
			$method .= "start=".$this->start."&";
			
		if($this->results != NULL)
			$method .= "results=".$this->results."&";
			
		if($this->fromdt != NULL)
			$method .= "fromdt=".$this->fromdt."&";
			
		if($this->todt != NULL)
			$method .= "todt=".$this->todt."&";
			
		if($this->meta != NULL)
			$method .= "date=".$this->meta;
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	// Hashes
	// Returns a change manifest of all posts. Call the update function to see if you need to fetch this at all.
	// This method is intended to provide information on changed bookmarks without the necessity of a complete download of all post data.
	// Each post element returned offers a url attribute containing an URL MD5, with an associated meta attribute containing the current change detection signature for that bookmark.
	function hashesPost() {
		$method = "/posts/all?hashes";
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	// Suggest
	//  Returns a list of popular tags, recommended tags and network tags for a user.
	// This method is intended to provide suggestions for tagging a particular url. 
	function suggestPost() {
		$method = "/posts/suggest";
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	
	
	
/********************************************************************************************************************************************************/

	// Tags
	
	// Get
	// Returns a list of tags and number of times used by a user.
	function getTags() {
		$method = "/tags/get";
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	// Delete
	// Delete an existing tag.
	// Arguments
	//		(required) tag : tag to delete
	// Example Response
	//		<result>done</result>
	function deleteTags() {
		$method = "/tags/delete?";
		
		if($this->tag != NULL)
			$method .= "tag=".$this->tag;
		else
			return false;
			
		$this->retour = $this->connect($method);
		return $this->retour;
	}
	
	// Rename
	// Rename an existing tag with a new tag name.
	// Arguments
	//		(required) old : tag to rename
	//		(required) new : new tag name
	// Example Response
	//		<result>done</result>
	function renameTags($old,$new) {
		$met                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                               