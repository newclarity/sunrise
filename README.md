#Sunrise

Forms & Fields for Post Types in the WordPress Admin

##IMPORTANT:
- Sunrise **is currently alpha-level software** and thus it's **API will change** before it is released as Sunrise v2.0 _(Don't say we didn't warn you!)_
- Many of **the glossary terms are tenative**; if you have ideas for better terms then **please propose your suggestions** using the GitHub issue tracker.

##Background
For those who care about this pre-history of Sunrise read on. Otherwise, jump to the [**QuickStart**](#quickstart).

###Conception and Sunrise-1
The concepts for Sunrise were developed in numerous projects and [at least one CMS product](http://greatjakes.com/our_approach.htm) over several years starting in mid 2010. That culminated in a heavily legacy version that we have on BitBucket as [Sunrise-1](https://bitbucket.org/newclarity/sunrise-1). Although we've published it publicly, it was never really _released_. 

Sunrise-1 was driven by client requirements which meant refactoring was rarely allotted the time necessary and is thus highly over-architected. But, we learned a lot of lessons while building Sunrise-1.

###Reimaging and _this_ Sunrise.
This implementation of Sunrise is light and tight compared to Sunrise-1, and it includes all the things we've learned about design of API libraries for WordPress since we started extending WordPress in 2010.

This Sunrise was built starting mid-February 2014 for a two-fold purpose:

1. For the feature needs of the pending relaunch of [PM-Sherpa](http://pm-sherpa.com), 
2. As an architecture study for [the WordPress Metadata team](http://make.wordpress.org/core/2014/02/28/metadata-project-meeting-notes/) [and on [GitHub](https://github.com/wordpress-metadata/)], and 
3. As as proposed best-practice for WordPress plugin/theme/library architecture at [HardcoreWP.com](http://hardcorewp.com) and for potential presentation at WordCamps and  instruction at WordPress training courses.

_This_ Sunrise will be released out of beta as Sunrise v2.0.

###About This Sunrise and WordPress' Early Load sunrise.php File

This Sunrise has **absolutely no relationship** with [the `sunrise.php` file recognized by WordPress during bootstrap](http://wordpress-hackers.1065353.n5.nabble.com/Use-case-for-sunrise-php-td32639.html). And as this Sunrise won't conflict at a technical level with WordPress' `sunrise.php` we're keeping the name we picked back in 2010 before we knew such a filename in the `/wp-content/` directory was special to WordPress. Because we like the name.

##QuickStart

###1. Installing Sunrise
While Sunrise can be used as a plugin in the `/wp-content/plugins/` directory it's really meant to be required by a website and thus should be added to a `/sunrise/` directory within `/wp-content/mu-plugins/`. 

Once you've added Sunrise into its own directory within the must-use plugins directory you'll also need a plugin loader file to load it. Here's one we use as `/wp-content/mu-plugins/plugin-loader.php`: 


    <?php
    /**
     * Plugin Name: {Your Site}'s Must-Use Plugins
     * Description: Contains the WordPress/PHP plugins for {Your Site}.
     */
    require(__DIR__ . '/sunrise/sunrise.php');


###2. Registering Forms and Fields:
Once you've got Sunrise installed you only need to register your Forms and Fields in an `'init'` hook. The following will add the three (3) fields `'website'`, `'tagline'` and '`blurb`' to the `pm_solution` page:

    <?php 
    /**
     * functions.php - The functions file for {Your Site}'s theme.
     */
    add_action( 'init', 'yoursite_init' );
    
    function yoursite_init() {
      
      Sunrise::register_form( 'pm_solution' );
      
      Sunrise::register_form_field( 'website', array(
        'type'  => 'url',
        'label' => __( 'Website', 'pm-sherpa' ),
        'html_placeholder' => 'http://www.example.com',
        'html_size'  => 50,
      ));
      
      Sunrise::register_form_field( 'tagline', array(
        'label' => __( 'Tagline', 'pm-sherpa' ),
        'html_size'  => 50,
      ));
      
      Sunrise::register_form_field( 'blurb', array(
        'type'  => 'textarea',
        'label' => __( 'Blurb', 'pm-sherpa' ),
      ));
    }

###3. There is No Step 3!
That's it, you're done! With the previous code in your theme's `functions.php` file _(or better yet in your own plugin)_ you would now have fields that look like so _(**NOTE**  we assume you already called `register_post_type()` to register the `'pm_solution'` post type):_

![](http://screenshots.newclarity.net/skitched-20140227-152353.png)


Of course **we don't actually recommend using global functions for your hooks**. Instead [see this blog post](http://hardcorewp.com/2012/using-classes-as-code-wrappers-for-wordpress-plugins/) to learn how to use classes as code wrappers for your WordPress Plugin.

##Usage

###Registering a Form
###Registering a Field to a Form
###Registering a Multiuse Field
###Adding a Multiuse Field to a Form



###Registering and Adding Multiuse Fields

###Registering and Using Field Prototypes

###Developing and Registering New Field Types Classes.

##Rules of Unqualified Object Type Evaluation
When Object Types are provided as strings they can either be in the format:

- `"{$object_type}/{$subtype}"` or 
- `"{$unqualified_object_type}"` 

For example:

- `"post/page"` or 
- `"my_custom_post_type"` or 
- `"comment"` 

Sunrise understands that `"my_custom_post_type"` expands to `"post/my_custom_post_type"` because _"my_custom_post_type"_ is _**not**_ one of the Reserved Unqualified Object Types. 

Conversely `"comment"` expands to `"comment/"` because it _**is**_ one of the Reserved Unqualified Object Types and _(currently)_ WordPress does not support the concept of a subtype for a Comment.

###Reserved Unqualified Object Types
Sunrise plans to allocate these names as Reserved Unqualified Object Types but expects to possibly add more before the version of Sunrise is released as non-beta:

- User
- Comment
- Option
- Term
- Taxonomy 
- Site
- Network


##Architecture

###The Sunrise Class
The Sunrise API is effectively controlled through the `Sunrise` class as a namespace along with several static methods. The static methods are known as Helper Methods and they can be called using this general syntax: 

- `Sunrise::method_name( <arg1>, <arg2>, ... <argN> )`

####Main Sunrise Class Methods
The following methods are the ones we expect will be used most often:

- `register_form()` - Registers a Form which is a container of fields. Forms are uniquely identified by their Object Type, Form Context and Form Name. [See reference.]()
- `register_form_field()` - Registers a Field for the most recently registered Form.  [See reference.]()
- `register_field()` - Registers a Multiuse Field that can be added to multiple forms using the `add_form_field()` method.  [See reference.]()
- `add_form_field()` - Adds a previously registered Multiuse Field to the most recently registered Form.  [See reference.]()

###The $args Pattern
Sunrise makes heavy use of what we call _"The $args Pattern"_. This pattern can be found in use in many aspects of the WordPress core code but by no means in all areas of WordPress core where it could be used.

###Late Fixup and Instantiation
Unlike `register_post_type()` and `register_taxonomy()` the methods for registering Forms and Fields in Sunrise delay most Fixup and most instantiation until the latest point in page load possible. And for places where it is possible to avoid Fixup and instantiation Sunrise bypasses Fixup and instantiation, such as the instantiation of Fields not used in any current Forms needed for the current page load.

###Uniquely Identifying Forms
Unlike Fields which can be uniquely identified with either their `field_name`, if they are Multiuse Fields, or by a Form and their `field_name` Forms do not have a single unique identifier other than the `form_index` assigned at the time the Form is registered. 

The `register_form()` method returns the registered `form_index` property which can be passed to `register_form_field()` if needed to override the Current Form

###Naming 
Consistent and rigourous naming is a critical part of the Sunrise architecture. In many  but not all cases the naming drives functionality so that in those cases the naming conventions must be used consistently in order for code to work correctly.

#### Property Name Prefixes
We have decided to prefix most of the properties of a class with the class' `VAR_PREFIX`; i.e. for Forms it's `'form_'` and for Fields it is `'field_'`. We do this in all cases that a field name otherwise would not have an underscore but we leave compound property names alone, i.e. `$field->form_name` vs. `$field->object_type`.  

However in special cases where the prefixed name _just does not feel right_ we add the property names to the class' `NO_PREFIX` constant using a pipe character (`'|'`) as a separator. Examples here include `$forms->_fields` _(an internal property of the Form)_, and `$field->value`.

We use the prefixed names like `$field->field_name` instead of the simpler `$field->name` for two (2) reasons:

1. It makes search and replace easier thus reducing errors introduced during inevitable code refactoring, and 
2. It enables values for multiple objects to be mixed within the same `$args` array, i.e. `field_` or no prefix for the Field itself, `html_` as a prefix for those properties that are specifically for the HTML attributes of the Field's Control, `label_` for Label specific properties, `label_html_` for the HTML properties of the Label's `<label>` element, and so on.

To reduce the burden of having to repeatedly type `'field_'` as a prefix when registering fields, and to reduce the resultant visual noise we allow you to drop the `field_` as keys in your `$args` arrays used for initialization unless you need to remove a rare ambiguity. 

We also _(plan to)_ implement `VAR_ALIASES` to allow certain properties such as `'html_placeholder'` to be specified as just `'placeholder'` in the `$args` array.

#### Method Naming: _property\_name()_ vs. _get\_property\_name()_
Many libraries _(including ones we developed) used the pattern `get_*()/set_*()` for internal property getters and setter but with Sunrise we decided to break tradition and go with something the looks simpler. So instead of `get_property_name()` for getters we use `property_name()` in most, but not all cases. For example, `$field->value()`.

We use `get_property_name()` for cases where we are generally doing a good bit more work to retrieve the value than just accessing an internal instance property, at least the first time the value is retrieved. 

Although theoretically classes should be black boxes and thus we theoretically should have a more consistent naming convention experience has taught us that in reality good developers simply cannot ignore that some operations are time-consuming and might have recursive side-effects. So a perfect example use of the `get_*()` pattern is `$form->get_fields()`.

...

###Internal Property Caching

###Location of Hooks
Hooks needed on page load are only adding by the core Sunrise class and by its Helper Classes. This makes autoloading of the remaining classes viable.

###Autoloading 

###Methods for Subclassing

- `default_args()`
- `pre_assign()`
- `initialize()`


###Class Constants
Sunrise uses many of it's Class Constants as metadata in a manner very similar to how [annotations are used in Java](http://stackoverflow.com/questions/24221/java-annotations) and how [attributes are used in .NET](http://stackoverflow.com/questions/20346/net-what-are-attributes). Sunrise's Class Constants allow base classes to inspect information about child classes and to operate differently based on those constants. 

- `VAR_PREFIX`
- `NO_PREFIX`
- `CONTROL_TAG`
- `HTML_TYPE`
- `FORM_CONTEXT`

Basically we could have used WordPress filters instead of Class Constants but in the few cases we use them Class Constants are used to both improve runtime performance and simplify the implementation of child classes for Forms, Fields, Features.

###The Sunrise_Base Class


###Target Platforms
- Sunrise is currently designed to **target HTML5** without loosing too much functionality in HTML4, 
- Sunrise **requires PHP 5.3** or later because it uses `get_called_class()` to provide Helper Classes and Methods. Note that this is not critical to the architecture but would require a build step using Grunt or similar given the current source file structure and layout.

##Glossary
Sunrise takes great pains to name aspects of its architecture and to be very consistent with the way in which those names are used. So here is the 

- **Form** - A conceptual wrapper for a collection of Fields. 
	- In some _(future)_ contexts a Form may be associated with an HTML `<form>`, such as when it's Form Context is `theme`, but in other cases, such as the only one currently implemented a Form is simply a collection of Fields  where there may be more than one Sunrise Form within a single HTML `<form>`. 
	- For Posts in the Form Context of `admin` _(which is currently the only use-case)_ a Form may either be displayed between the title/URL and the TinyMCE rich text editor for the post content or within a WordPress metabox. Forms with the default Form Name of 'main' are to be displayed above post content and the others in metaboxes.
- **Field** - A container of Features that collectively represent an item of data or information that should be persisted for an Object when the Object is persisted to the database _(or wherever else it might be persisted in the future.)_
	- Fields are comprised of a collection of Features: Control, Label, Help, Infobox and Message.
- **Feature** - An aspect of a Field that provides some functional part to the field. At the point only Control and Label are implemented.
	- **Control** - A composition of HTML elements that are collectively used to present a data entry field, typically an `<input>`, `<select>` or `<textarea>`. 
	- **Label** - A composition of HTML elements that are collectively used to present a `<label>` associated with a Control above or to the left of the Control.
	- **Help** - **_(pending)_** A composition of HTML elements that are collectively used to present persistent help text for a field to the user, typically below the field.
	- **Infobox** - **_(pending)_** A composition of HTML elements that are collectively used to present non-persistent help text for a field to the user, typically on-click or on-hover of an "info" icon.
	- **Message** - **_(pending)_** A composition of HTML elements that are collectively used to present a warning or error message on field validation.

- **Object** - Generic term used to refer collectively to named well-known entities withing WordPress such as Post, User, Comment, Taxonomy, Term, etc. Currently Sunrise only supports Posts but plans to support the others in the near future.

- **Object Type** - A classifier for an object type that has both object_type and subtype properties. An Object Type can be representing using a string of the format `"{$object_type}/{$subtype}"` or as an instance of the `Sunrise_Object_Classifier`. 
	- The class constructor accepts a string formatted to represent an Object Type and the constructor by delegation parses it into the two (2) properties `$classifier->object_type` and `$classifier->subtype`. The class also has a `__ToString()` method that allows the Object Type's value to be cast to a correctly formatted string when that is needed.

- **Unqualified Object Types** - A classifier string that does not contain a slash but still uniquely identifies the Object Type by following rules for evaluation. See the section titled _"[_Rules of Unqualified Object Type Evaluation_](#rulesofunqualifiedobjecttypeevaluation)."_

- **Form Context** - A simple string identifying the targeted location for the form, i.e. `'admin'` or `'theme'`.  Together with the Form's Object Type and form name the Form Context gives Sunrise enough information to determine where the Form would be presented to the user.

- **Static Hooks** - Although they may sound exotic Static Actions and Static Filters are simply a shorthand for regular WordPress actions and filters that use a `static` methods of a class for the callback.
	- For example, the following two lines have the exact same result but the first looks a lot cleaner, in our opinion at least:
	
        <pre><code>Sunrise::add_static_action( 'init' );
        add_action( 'init', array( \_\_CLASS\_\_, '_init' ) );</code></pre>   
        
	- Note that using the `add_static_action()` method enforces an underscore prefix on methods to indicate the method should be considered _non-public_ for external users of the class. For more on Static Actions and Filters such as how to handle priorities other than 10 and how to change the hook name [see details]().

- **Multiuse Field** - 
- **Helper Classes and Methods** - 
- **Storage** -
- **Class Constants** -
- **Current Form** - `Sunrise::form_index()


##Validation

##Class Reference

###Core Class
- `Sunrise` - 

###Supporting Classes
- `Sunrise_Object_Classifier` - 
- `Sunrise_Html_Element` - 
- `Sunrise_Metabox`- 

###Form Class(es)
- `Sunrise_Post_Admin_Form` - 

###Field Classes
- `Sunrise_Text_Field` - 
- `Sunrise_Textarea_Field` - 
- `Sunrise_Url_Field` - 

###Feature Classes
- `Sunrise_Control_Feature` - 
- `Sunrise_Label_Feature` - 
- `Sunrise_Help_Feature` - 
- `Sunrise_Infobox_Feature` - 
- `Sunrise_Message_Feature` - 

###Helper Classes
- `_Sunrise_Forms_Helper` - 
- `_Sunrise_Fields_Helper` - 
- `_Sunrise_Html_Elements_Helper` - 
- `_Sunrise_Post_Admin_Forms_Helper` - 
- `_Sunrise_Posts_Helper` - 


