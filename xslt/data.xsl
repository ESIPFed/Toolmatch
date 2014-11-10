<xsl:stylesheet version="2.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns="http://www.w3.org/1999/xhtml"
		xmlns:res="http://www.w3.org/2005/sparql-results#"
		xmlns:fn="http://www.w3.org/2005/xpath-functions"
		exclude-result-prefixes="res xsl">

<xsl:template match="/">
  <xsl:variable name="current" select="res:sparql/res:results/res:result"/>
  <xsl:variable name="data">
    <xsl:apply-templates select="$current/res:binding[@name='data']"/>
  </xsl:variable>
  <xsl:variable name="label">
    <xsl:apply-templates select="$current/res:binding[@name='label']"/>
  </xsl:variable>
  <xsl:variable name="doi">
    <xsl:apply-templates select="$current/res:binding[@name='doi']"/>
  </xsl:variable>
  <xsl:variable name="url">
    <xsl:apply-templates select="$current/res:binding[@name='url']"/>
  </xsl:variable>
  <xsl:variable name="description">
    <xsl:apply-templates select="$current/res:binding[@name='description']"/>
  </xsl:variable>
  <xsl:variable name="format">
    <xsl:apply-templates select="$current/res:binding[@name='format']"/>
  </xsl:variable>
  <xsl:variable name="convention">
    <xsl:apply-templates select="$current/res:binding[@name='convention']"/>
  </xsl:variable>
    <span style="font-weight:bold;font-size:20pt;float:left;"><xsl:copy-of select="$label"/></span>
    <span style="float:right;margin-right:20px;"><a href="http://toolmatch.esipfed.org/delete.php?data={$label}"><img src="/images/delete_icon.png" alt="Delete Data Collection" title="Delete Data Collection" height="24px" width="24px" onClick="return confirmDelete()" /></a></span>
    <span style="float:right;margin-right:5px;"><a href="http://toolmatch.esipfed.org/dataform.php?uri={$data}"><img src="/images/pencil-icon-128.png" alt="Edit Data Collection" title="Edit Data Collection" height="24px" width="24px" /></a></span>
	<br/><br/>
	<span style="font-weight:bold;">DOI: </span><xsl:copy-of select="$doi"/>
	<br/>
	<span style="font-weight:bold;">Access URL: </span><xsl:copy-of select="$url"/>
	<br/>
	<span style="font-weight:bold;">Description: </span><xsl:copy-of select="$description"/>
	<br/>
	<span style="font-weight:bold;">Format: </span><xsl:copy-of select="$format"/>
	<br/>
	<span style="font-weight:bold;">Convention: </span><xsl:copy-of select="$convention"/>
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
