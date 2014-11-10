<xsl:stylesheet version="2.0"
		xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
		xmlns="http://www.w3.org/1999/xhtml"
		xmlns:res="http://www.w3.org/2005/sparql-results#"
		xmlns:fn="http://www.w3.org/2005/xpath-functions"
		exclude-result-prefixes="res xsl">

<xsl:template match="/">
  <xsl:for-each select="res:sparql/res:results/res:result">
    <xsl:variable name="current" select="."/>
	<xsl:variable name="tool">
	  <xsl:apply-templates select="$current/res:binding[@name='tool']"/>
	</xsl:variable>
	<xsl:variable name="label">
	  <xsl:apply-templates select="$current/res:binding[@name='label']"/>
	</xsl:variable>
	<xsl:variable name="description">
	  <xsl:apply-templates select="$current/res:binding[@name='description']"/>
	</xsl:variable>
    <div class="instance_row">
      <input type="button" class="instance_title" onclick="showHideToolInfo('{$label}')" value="{$label}"/>
	  <div style="float:right;"><a href="http://toolmatch.esipfed.org/delete.php?tool={$label}"><img src="/images/delete_icon.png" alt="Delete Tool" title="Delete Tool" height="24px" width="24px" onClick="return confirmDelete()" /></a></div>
      <div style="float:right;"><a href="http://toolmatch.esipfed.org/toolform.php?uri={$tool}"><img src="/images/pencil-icon-128.png" alt="Edit Tool" title="Edit Tool" height="24px" width="24px" /></a></div>
      <div id="{$label}" style="display:none;">
		<xsl:copy-of select="$description"/><xsl:text>  </xsl:text><a id="more_info" href="http://toolmatch.esipfed.org/tool.php?uri={$tool}">See Full Info</a><br/><br/>
      </div>
    </div>
	<script>
	function confirmDelete() {
		var x = window.confirm("Are you sure you want to delete this tool?");
		return x;
	}
	</script>
  </xsl:for-each>
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
