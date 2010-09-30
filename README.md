# QueryPath Tasks for Phing

This package provides Phing tasks that let you use QueryPath to process XML or HTML.

## Installation

* [QueryPath](http://querypath.org) must be installed before this will work.
* [Phing](http://phing.info) must be installed for this to work.

**With PEAR**:

    $ pear channel-discover pear.querypath.org
    $ pear install querypath/QueryPath
    $ pear install querypath/Phing-QueryPath

**From Source**

1. Get the source
  * You can clone git
  * You can download from GitHub
2. Put the code in a path accessible to Phing

## Usage

To use:

Edit your `build.xml` file to map the `QueryPathReplacementTask.php` to a task element:

    <!-- You might need some variant of this if Phing cannot find QueryPath. -->
    <!-- includepath classpath="path/to/QueryPath"/ -->

    <!-- You might need some variant of this if Phing cannot find the task. -->
    <!-- includepath classpath="path/to/Phing-QueryPath"/ -->

    <!-- Task definition for QueryPathReplacementTask. -->
    <taskdef classname="PhingQueryPath.Task.QueryPathReplacementTask" name="qpreplace"/>
    
Now, somewhere else in your build.xml, you can use `qpreplace` to run replacements:

    <qpreplace>
      <fileset dir="path/to/xml/files">
        <include name="*.xml"/>
      </fileset>
      <rule selector="targetOne">FOO</rule>
      <rule selector="div>targetTwo">FOO2</rule>
      <rule selector="#three>targetThree">FOO3</rule>
    </qpreplace>

The above reads in all of the XML files as a fileset, and then applies the rule replacements to each file. Note that the `selector` attributes are CSS 3 selectors (just like jQuery and QueryPath use), and the text value is what will replace the selector. Of course you can use Phing variables in either case. In other words, this would be a valid rule:

    <property name="selector" value="#content > div.lede:first"/>
    <property name="replacement" value="can haz cheezburger?"/>
    <qpreplace>
      <fileset dir="path/to/xml/files">
        <include name="*.xml"/>
      </fileset>
      <rule selector="${selector}">${replacement}</rule>
    </qpreplace>

That's all there is to using this task. It can be especially useful for post-processing XML file that are generated automatically during a phing build (for example PearPackage2's `package.xml` file).