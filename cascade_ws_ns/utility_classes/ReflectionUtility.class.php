<?php
/**
  * Author: Wing Ming Chan
  * Copyright (c) 2016 Wing Ming Chan <chanw@upstate.edu>
  * MIT Licensed
  * Modification history:
  * 8/23/2016 Added more documentation and split private methods.
  * 8/16/2016 Added getFunctionSignature and showFunctionSignature.
  * 8/15/2016 Added getClassDocumentation.
  * 8/14/2016 Class being tested with extra comment.
  * 8/13/2016 Methods added.
  * 8/12/2016 File created.
 */
namespace cascade_ws_utility;

use cascade_ws_asset     as a;
use cascade_ws_property  as p;
use cascade_ws_constants as c;

/**
<documentation><description><h2>Introduction</h2>
<p>This class can be used to expose class and method information of any class, using reflection.
All methods provided in this class are static. For practical purposes, use only those <code>show</code> methods. The <code>get</code>
methods are used to generate documentation pages in the cascade-admin site.
The value of the <code>$obj</code> variable in these methods can be an object of any class,
or a string (the full name of a class to be inspected).</p>
</description></documentation>
*/
class ReflectionUtility
{
/**
Returns the class information given right before the class definition.
@param mixed $obj A string (the class name) or an object
<documentation><description><p>Returns the class information given right before the class definition.
If <code>true</code> is passed in for <code>$with_hr</code>, then an &lt;hr/&gt; element will be added after the
class information.</p></description>
<example>echo u\ReflectionUtility::getClassDocumentation( "cascade_ws_utility\ReflectionUtility", true );</example>
<return-type>string</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getClassDocumentation( $obj, bool $with_hr=false ) : string
    {
        $class_doc = "";
        $r = new \ReflectionClass( $obj );
        $class_doc .= self::getClassInfo( $obj, $r ) . "<h2>Class API</h2>";
        
        $methods = $r->getMethods();
        
        $class_doc .= S_UL;
        
        foreach( $methods as $method )
        {
            // skip private methods
            if( self::getMethodSignature( $method ) == "" )
                continue;
            
            $class_doc .= S_LI . 
                S_CODE . self::getSignature( $method ) . E_CODE .
                self::getMethodXmlValue( $obj, $method->getName(), "description", "", "", $method ) .
                self::getMethodXmlValue( $obj, $method->getName(), "example", S_PRE, E_PRE, $method ) .
                E_LI;
        }
        
        $class_doc .= E_UL;
        
        if( $with_hr )
        	$class_doc .= HR;
        
        return $class_doc;
    }

/**
Returns the class information given right before the class definition.
@param mixed $obj A string (the class name) or an object
@param ReflectionClass The ReflectionClass object
<documentation><description><p>Returns the class information given right before the class definition.
If <code>$r</code> is set, then <code>$obj</code> will be ignored.
If <code>true</code> is passed in for <code>$with_hr</code>, then an &lt;hr/&gt; element will be added after the
class information.</p></description>
<example>echo u\ReflectionUtility::getClassInfo( "cascade_ws_utility\ReflectionUtility", NULL, true );</example>
<return-type>string</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getClassInfo( $obj, \ReflectionClass $r=NULL, $with_hr=false ) : string
    {
        $class_info = "";
        
        if( !isset( $r ) )
            $r = new \ReflectionClass( $obj );
            
        $class_info = self::getClassXmlValue( $r->getDocComment(), "description" );
        
        if( $with_hr )
        	$class_info .= HR;
        
        return $class_info;
    }
    
/**
Returns the class name.
@param mixed $obj A string (the class name) or an object
@return string The class name
<documentation><description><p>Returns the class name of the value of <code>$obj</code>.</p></description>
<example>echo u\ReflectionUtility::getClassName( $service );</example>
<return-type>string</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getClassName( $obj ) : string
    {
        $r = new \ReflectionClass( $obj );
        return $r->getName();
    }
    
/**
Returns the signature of a function.
@param ReflectionFunction $function The function object
@return string The signature of a function
<documentation><description><p>Returns the signature of a function.</p></description>
<example>echo u\ReflectionUtility::getFunctionSignature( new \ReflectionFunction( "str_getcsv" ) );</example>
<return-type>string</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getFunctionSignature( \ReflectionFunction $function ) : string
    {
        return self::getSignature( $function );
    }
    
/**
Returns the named method (a ReflectionMethod object).
@param mixed $obj A string (the class name) or an object
@return ReflectionMethod The method
<documentation><description><p>Returns the named method (a <code>ReflectionMethod</code> object).</p></description>
<example>u\DebugUtility::dump( u\ReflectionUtility::getMethod( $service, "read" ) );</example>
<return-type>ReflectionMethod</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getMethod( $obj, string $method_name ) : \ReflectionMethod 
    {
        $r = new \ReflectionClass( $obj );
        return $r->getMethod( $method_name );
    }
    
/**
Returns all the textual information of a method provided right before the method definition.
@param ReflectionMethod $method The method object
@return string The info string
<documentation><description><p>Returns all the textual information of a method provided right before the method definition.
This information includes comments and XML markups in the library files.</p></description>
<example>echo u\ReflectionUtility::getMethodInfo( 
    new \ReflectionMethod( "cascade_ws_utility\ReflectionUtility", "getMethodInfo" ) );</example>
<return-type>string</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getMethodInfo( \ReflectionMethod $method ) : string
    {
        $method_info = self::getMethodSignature( $method ) . "\n";
        $method_info .= $method->getDocComment();
        
        return $method_info;
    }
        
/**
Returns the all the textual information of a method.
@param mixed $obj A string (the class name) or an object
@param string $method_name The method name
@return string The info string
<documentation><description><p>Returns all the textual information of a method provided right before the method definition.
This information includes comments and XML markups in the library files.</p></description>
<example>$info = u\ReflectionUtility::getMethodInfoByName( $service, "getType" );</example>
<return-type>string</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getMethodInfoByName( $obj, string $method_name ) : string
    {
        $r        = new \ReflectionClass( $obj );
        $method   = $r->getMethod( $method_name );
        
        return self::getMethodInfo( $method );
    }
    
/**
Returns an array of methods of the class.
@param mixed $obj A string (the class name) or an object
@return array The array containing all methods
<documentation><description><p>Returns an array of methods (<code>ReflectionMethod</code> objects)
of the class.</p></description>
<example>u\DebugUtility::dump( u\ReflectionUtility::getMethods( $service ) );</example>
<return-type>array</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getMethods( $obj ) : array
    {
        $r = new \ReflectionClass( $obj );
        return $r->getMethods();
    }
    
/**
Returns the signature of a method.
@param ReflectionMethod $method The method object
@return string The info string
<documentation><description><p>Returns the signature of a method.
The returned string does not include exception information.</p></description>
<example>echo u\ReflectionUtility::getMethodSignature( 
    new \ReflectionMethod( "cascade_ws_utility\ReflectionUtility", "getMethodSignature" ) );</example>
<return-type>string</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getMethodSignature( \ReflectionMethod $method ) : string
    {
        return self::getSignature( $method );
    }
    
/**
Returns the signature of a method.
@param mixed $obj A string (the class name) or an object
@param string $method_name The method name
@return string The info string
<documentation><description><p>Returns the signature of a method.
The returned string does not include exception information.</p></description>
<example>echo u\ReflectionUtility::getMethodSignatureByName( 
    "cascade_ws_utility\ReflectionUtility", "getMethodSignature" ), BR;</example>
<return-type>string</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getMethodSignatureByName( $obj, string $method_name ) : string
    {
        $r        = new \ReflectionClass( $obj );
        $method   = $r->getMethod( $method_name );
        
        return self::getSignature( $method );
    }
    
/**
Returns an unordered list of signatures of methods defined in the class.
@param mixed $obj A string (the class name) or an object
@return string The string containing information of all methods
<documentation><description><p>Returns an unordered list of signatures of methods defined in the class.
The returned string does not include exception information.</p></description>
<example>u\ReflectionUtility::showMethodSignatures( 
    "cascade_ws_utility\ReflectionUtility", true );</example>
<return-type>string</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function getMethodSignatures( $obj ) : string
    {
        $methods = self::getMethods( $obj );
        $method_info = S_UL;
        
        foreach( $methods as $method )
        {
            // skip empty string
            if( self::getMethodSignature( $method ) == "" )
                continue;
                
            $method_info .= S_LI . S_CODE . self::getMethodSignature( $method ) . E_CODE . E_LI;
        }
        
        $method_info .= E_UL;
        
        return $method_info;
    }
    
/**
Displays the class information given right before the class definition.
@param mixed $obj A string (the class name) or an object
<documentation><description><p>Displays an unordered list of information of methods defined in the class.
If <code>true</code> is passed in for <code>$with_hr</code>, then a horizontal rule will be output after the
class information.</p></description>
<example>u\ReflectionUtility::showClassInfo( "cascade_ws_utility\ReflectionUtility", true );</example>
<return-type>void</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function showClassInfo( $obj, bool $with_hr=false )
    {
        echo self::getClassInfo( $obj );
        if( $with_hr ) echo HR;
    }
    
/**
Displays the signature of a function.
@param mixed $obj A string (the class name) or an object
@param string $method_name The method name
<documentation><description><p>Displays the signature of a function.
If <code>true</code> is passed in for <code>$with_hr</code>, then a horizontal rule will be output after the
function signatures.</p></description>
<example>u\ReflectionUtility::showFunctionSignature( "str_replace", true );</example>
<return-type>void</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function showFunctionSignature( string $function_name, bool $with_hr=false )
    {
        $func = new \ReflectionFunction( $function_name );
        
        echo self::getFunctionSignature( $func );
        if( $with_hr ) echo HR;
    }
    
/**
Displays the description of a method.
@param mixed $obj A string (the class name) or an object
@param string $method_name The method name
<documentation><description><p>Displays the description of a method.
If <code>true</code> is passed in for <code>$with_hr</code>, then a horizontal rule will be output after the
method description.</p></description>
<example>u\ReflectionUtility::showMethodDescription(
    "cascade_ws_utility\ReflectionUtility", "getMethodSignature", true );</example>
<return-type>void</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function showMethodDescription( $obj, string $method_name, bool $with_hr=false )
    {
        echo self::getMethodXmlValue( $obj, $method_name, "description", S_P, E_P );
        if( $with_hr ) echo HR;
    }
    
/**
Displays an example of how to use a method.
@param mixed $obj A string (the class name) or an object
@param string $method_name The method name
<documentation><description><p>Displays an example of how to use a method.
If <code>true</code> is passed in for <code>$with_hr</code>, then a horizontal rule will be output after the
method example.</p></description>
<example>u\ReflectionUtility::showMethodExample( "cascade_ws_utility\ReflectionUtility", "showMethod" );</example>
<return-type>void</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function showMethodExample( $obj, string $method_name, bool $with_hr=false )
    {
        echo self::getMethodXmlValue( $obj, $method_name, "example", S_PRE, E_PRE );
        if( $with_hr ) echo HR;
    }

/**
Displays exception information of a method.
@param mixed $obj A string (the class name) or an object
@param string $method_name The method name
<documentation><description><p>Displays exception information of a method.
If <code>true</code> is passed in for <code>$with_hr</code>, then a horizontal rule will be output after the
method exception information.</p></description>
<example>u\ReflectionUtility::showMethodException(
    "cascade_ws_utility\ReflectionUtility", "getMethodInfo", true );</example>
<return-type>void</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function showMethodException( $obj, string $method_name, bool $with_hr=false )
    {
        echo self::getMethodXmlValue( $obj, $method_name, "exception", S_PRE, E_PRE );
        if( $with_hr ) echo HR;
    }

/**
Displays all textual information give right before the definition of a method.
@param mixed $obj A string (the class name) or an object
@param string $method_name The method name
<documentation><description><p>Displays all textual information give right before the definition of a method,
including XML markups.
If <code>true</code> is passed in for <code>$with_hr</code>, then a horizontal rule will be output after the
method information.</p></description>
<example>u\ReflectionUtility::showMethodInfo(
    "cascade_ws_utility\ReflectionUtility", "getMethodInfo", true );</example>
<return-type>void</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function showMethodInfo( $obj, string $method_name, bool $with_hr=false )
    {
        echo S_PRE,
            self::getMethodInfo( self::getMethod( $obj, $method_name ) ),
            E_PRE;
        if( $with_hr ) echo HR;
    }
    
/**
Displays the return type of a method.
@param mixed $obj A string (the class name) or an object
@param string $method_name The method name
<documentation><description><p>Displays the return type of a method.
If <code>true</code> is passed in for <code>$with_hr</code>, then a horizontal rule will be output after the
method return type.</p></description>
<example>u\ReflectionUtility::showMethodReturnType(
    "cascade_ws_utility\ReflectionUtility", "getMethodInfo", true );</example>
<return-type>void</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function showMethodReturnType( $obj, string $method_name, bool $with_hr=false )
    {
        echo self::getMethodXmlValue( $obj, $method_name, "return-type", S_PRE, E_PRE );
        if( $with_hr ) echo HR;
    }


/**
Displays the signature of a method.
@param mixed $obj A string (the class name) or an object
@param string $method_name The method name
<documentation><description><p>Displays the signature of a method. 
The information shown includes exception information.
If <code>true</code> is passed in for <code>$with_hr</code>, then a horizontal rule will be output after the
method signature.</p></description>
<example>u\ReflectionUtility::showMethodSignature( 
    "cascade_ws_utility\ReflectionUtility", "showMethodInfo", true );</example>
<return-type>void</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function showMethodSignature( $obj, string $method_name, bool $with_hr=false )
    {
        echo self::getMethodSignature( self::getMethod( $obj, $method_name ) );
        if( $with_hr ) echo HR;
    }
    
/**
Displays an unordered list of signatures of methods defined in the class.
@param mixed $obj A string (the class name) or an object
<documentation><description><p>Displays an unordered list of signatures of methods defined in the class.
The information shown includes exception information.
If <code>true</code> is passed in for <code>$with_hr</code>, then a horizontal rule will be output after the
method signatures.</p></description>
<example>u\ReflectionUtility::showMethodSignatures( 
    "cascade_ws_utility\ReflectionUtility", true );</example>
<return-type>void</return-type>
<exception>ReflectionException</exception>
</documentation>
*/
    public static function showMethodSignatures( $obj, bool $with_hr=false )
    {
        echo self::getMethodSignatures( $obj );
        if( $with_hr ) echo HR;
    }
    
    private static function getSignature( $method ) : string
    {
        $method_info = "";
        $type        = gettype( $method );
        
        $class = ( method_exists( $method, "getDeclaringClass" ) ?
            $method->getDeclaringClass()->getName() . "::" : "" );
        
        $modifiers = ( method_exists( $method, "getDeclaringClass" ) ?
            implode( ' ', \Reflection::getModifierNames( $method->getModifiers() ) ) :
            "" );
            
        $return_type = ( method_exists( $method, "getReturnType" )  ? 
            $method->getReturnType() : "" );
        
        if( gettype( $method ) == "ReflectionMethod" )
			$return_type = ( $return_type != "" ? $return_type :  
				self::getMethodXmlValue( NULL, "", "return-type", "", "", $method )
			 );
         
        if( $return_type == c\M::INFORMATION_NOT_AVAILABLE )
             $return_type = "";
            
        $throws = "";
         
        if( method_exists( $method, "getDeclaringClass" ) )
			$throws = ( (
				self::getMethodXmlValue( NULL, "", "exception", "", "", $method ) == "" ||
				self::getMethodXmlValue( NULL, "", "exception", "", "", $method ) == c\M::INFORMATION_NOT_AVAILABLE )
				? "" : " throws " . self::getMethodXmlValue( NULL, "", "exception", "", "", $method ) );
        
        $method_info .=
            $modifiers .
            ( $return_type != "" ? " " . $return_type : "" ) .
            $method_info . " " .
            $class .
            $method->getName() . "(";
        
        $num_of_params = $method->getNumberOfParameters();
        
        if( $num_of_params )
        {
            $params      = $method->getParameters();
            $count       = 1;
            
            foreach( $params as $param )
            {
                $param_type = ( method_exists( $param, "getType" )  ? 
                    $param->getType() : "" );
                    
                $method_info .=
                    ( $param_type != ""  ? " " . $param_type : "" ). 
                    " $" . $param->getName();
                
                if( method_exists( $method, "getDeclaringClass" ) && $param->isOptional() )
                {
                    // ReflectionException: Cannot determine default value for internal functions
                    try
                    {   
                        $default_value = $param->getDefaultValue();
                
                        if( isset( $default_value ) )
                        {
                            if( $default_value == 1 && $param_type == "bool" )
                            {
                                $method_info .= " = true";
                            }
                            elseif( $default_value == 0 &&  $param_type == "bool" && $default_value != "" )
                            {
                                $method_info .= " = false";
                            }
                            elseif( $default_value == "" )
                            {
                                if( $default_value === "" )
                                    $method_info .= " = \"\"";
                                else
                                    $method_info .= " = false";
                            }
                            else
                            {
                                $method_info .= " = $default_value";
                            }
                        }
                        else
                        {
                            $method_info .= ' = NULL';
                        }
                    }
                    catch( \ReflectionException $e )
                    {
                        // do nothing
                    }
                }
                    
                if( $count < $num_of_params )
                {
                    $method_info .= ",";
                }
                $count++;
            }
            $method_info .= " ";
        }
        
        $method_info .= ")" . $throws;
        
        if( strpos( $modifiers, "private" ) !== false )
            return "";
        
        return trim( $method_info );
    }
    
    private static function getClassXmlValue( string $class_info, string $ele_name ) : string
    {
    	return self::getXmlValue( $class_info, $ele_name );
    }
    
    private static function getMethodXmlValue( 
        $obj, string $method_name, string $ele_name, string $s_html, string $e_html, \ReflectionMethod $method=NULL ) : string
    {
        // retrieve the method documentation
        if( !isset( $method ) )
            $method  = self::getMethod( $obj, $method_name );
            
        $xml_str = $method->getDocComment();
        
        return $s_html . self::getXmlValue( $xml_str, $ele_name ) . $e_html;
    }
    
    private static function getXmlValue( string $xml_str, string $ele_name ) : string
    {
        // chop off everything before the first <
        $xml_str = substr( $xml_str, strpos( $xml_str, "<" ) );
        // trim */
        $xml_str = str_replace( "*/", "", $xml_str );
        // create the SimpleXMLElement object
        try
        {
            $xml_ele = new \SimpleXMLElement( $xml_str );
            
            // look for the element and return the formatted text
            foreach( $xml_ele->children() as $child )
            {
                if( $child->getName() == $ele_name )
                {
                    return $child->asXML();
                }
            }
            return c\M::INFORMATION_NOT_AVAILABLE;
        }
        catch( \Exception $e )
        {
            return c\M::INFORMATION_NOT_AVAILABLE;
        }
    }
}