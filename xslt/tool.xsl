<xsl:stylesheet version="2.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns="http://www.w3.org/1999/xhtml"
		xmlns:res="http://www.w3.org/2005/sparql-results#"
		xmlns:fn="http://www.w3.org/2005/xpath-functions"
		exclude-result-prefixes="res xsl">

<xsl:template match="/">
  <xsl:variable name="current" select="res:sparql/res:results/res:result"/>
  <xsl:variable name="tool">
    <xsl:apply-templates select="$current/res:binding[@name='tool']"/>
  </xsl:variable>
  <xsl:variable name="label">
    <xsl:apply-templates select="$current/res:binding[@name='label']"/>
  </xsl:variable>
  <xsl:variable name="description">
    <xsl:apply-templates select="$current/res:binding[@name='description']"/>
  </xsl:variable>
  <xsl:variable name="image">
    <xsl:apply-templates select="$current/res:binding[@name='image']"/>
  </xsl:variable>
  <xsl:variable name="page">
    <xsl:apply-templates select="$current/res:binding[@name='page']"/>
  </xsl:variable>
  <xsl:variable name="version">
    <xsl:apply-templates select="$current/res:binding[@name='version']"/>
  </xsl:variable>
    <span class="page_title" style="font-weight:bold;font-size:20pt;float:left;"><xsl:copy-of select="$label"/>
    <span style="float:right;margin-right:20px;"><a href="http://toolmatch.esipfed.org/delete.php?tool={$label}"><img src="/images/delete_icon.png" alt="Delete Tool" title="Delete Tool" height="24px" width="24px" onClick="return confirmDelete()" /></a></span>
    <span style="float:right;margin-right:5px;"><a href="http://toolmatch.esipfed.org/toolform.php?uri={$tool}"><img src="/images/pencil-icon-128.png" alt="Edit Tool" title="Edit Tool" height="24px" width="24px" /></a></span></span>
	<br/><br/><br/>
	<img style="width:150px;" src="{$image}"/>
	<br/><br/>
	<span style="font-weight:bold;">Description: </span><xsl:copy-of select="$description"/>
	<br/>
	<span style="font-weight:bold;">Version: </span><xsl:copy-of select="substring-after($version,'_')"/>
	<br/>
	<span style="font-weight:bold;">Available at: </span><a href="{$page}" target="_blank"><xsl:copy-of select="$page"/></a>
	<br/>
  <script>
	function confirmDelete() {
		var x = window.confirm("Are you sure you want to delete this tool?");
		return x;
	}
  </script>

</xsl:template>

  <xsl:template match="res:literal">
    <xsl:variable name="cell-value">
      <xsl:value-of select="text()"/>
    </xsl:variable>
    <xsl:choose>
	  <xsl:when test="starts-with($cell-value,'http:') or starts-with($cell-value,'https:')">
	    <a href="{$cell-value}"><xsl:value-of select="$cell-value"/></a>
	  </xsl:when>
      <xsl:otherwise>
	    <xsl:value-of select="$cell-value"/>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template match="res:uri">
    <xsl:value-of select="text()"/>
  </xsl:template>

</xsl:stylesheet>
