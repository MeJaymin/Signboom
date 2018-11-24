<%@ Page Language="VB" ContentType="text/html" ResponseEncoding="iso-8859-1" %>
<%@ Import Namespace="System.IO"%> 
<%@ Import Namespace="Microsoft.VisualBasic"%> 

<!--#INCLUDE FILE="UlResult.aspx" -->

<script runat="server">

  ' Those below don't need to be global, but it's convenient to have them here for easy editing.
  dim gblUploadDir As String = "C:\uploads\"
  dim gblCompleteDir As String = "C:\Orders\"
  dim gblPacklistDir as string = "C:\Orders\PackList\"
  dim gblUploadSummary as Ulresult
  dim finew as FileInfo

' Open and parse the summary file for this order.
Private Sub Page_Load(ByVal sender As System.Object, ByVal e As System.EventArgs) 

  ' don't handle the page more than once (Alison, Jan 2018)
  ' IsPostBack: Gets a value that indicates whether the page is being rendered 
  ' for the first time or is being loaded in response to a postback.
  If IsPostBack Then Exit Sub

  ' Work out filename of the order summary file.
  Dim szUploadSummary As String = Request("Token")
  Dim szSummFile As String = gblUploadDir & szUploadSummary & ".ajmSumm"

  ' The ULResult class provides an easy way to read and parse the summary file.  
  gblUploadSummary = New ULResult(szSummFile, ULResult.FileDisposition.Delete)

  ProcessOrder()
  Response.Redirect("http://www.signboom.com/orderthanks.php?order=" & Request("orderid") & "&dbToken=" & Request("dbToken"))
  
End Sub


' Go through list of files in the order and process each.
Private Sub ProcessOrder() 
    
  Dim i as Integer
  for i = 1 to gblUploadSummary.Files.Count
    if gblUploadSummary.Files(i).Name <> ""
      Dim fi as FileInfo = New FileInfo(gblUploadSummary.Files(i).Name)
      finew = New FileInfo(gblUploadSummary.Files(i).SourceName)
      if Path.GetFileName(Request("fshipdocname")) = gblUploadSummary.Files(i).SourceName Then
        ProcessShippingDoc(fi)
      else
        ProcessOrderFile(i, fi)
      end if
    end if
  next

End Sub


' Process one of the files in the order.
Private Sub ProcessOrderFile(i as Integer, fi as FileInfo)

  ' Working variables
  Dim j as Integer
  Dim szWork as String
  Dim szExt as String = finew.Extension

  ' From the order-wide info we get the ready date, service, delivery, city, and customer.
  dim deliverytype as String = Request("fpickuptype").Trim    ' will be PPD, PICK or PACK
  dim accountname as String = Request("acctid").Trim 
  dim city as String = Request("txtcity").Trim 

  ' get tprod contents for the line being processed; exit if tprod is blank
  dim pr as string = Request("tprod" & i.ToString)
  if pr = "" then Exit Sub

  ' explore the array, using tildas as separators
  dim Items() as string = pr.split("~")

  ' Set up the indeces into the array; make sure they match those set up in allorder_yymmdd.js for wsdata.
  Dim prodidx = 0       ' Product code like COR04 or SAV. 
  Dim thickidx = 1      ' Was STD/CUS (duplicate of index 13). Now thickness.
  Dim heightidx = 2     ' Height of sign.
  Dim widthidx = 3      ' Width of sign.
  Dim lamidx = 4        ' Was linear feet. Now lamination.
  Dim quanidx = 5       ' Quantity of signs to print.
  Dim fileidx = 6       ' Filename the user gave their file. Without the path.
  Dim linetotidx = 7    ' Total line cost.
  Dim dctcostidx = 8    ' Total discount amount in dollars.
  Dim layeridx = 9      ' Used to be % waste. Now layers.
  Dim pagesidx = 10     ' Was product code. Now pages.
  Dim notesidx = 11     ' Was full product name. Now line item notes (YES or NO).
  Dim ocodeidx = 12     ' Just STD or CUS.
  Dim odescidx = 13     ' Starts with STD/CUS. Followed by details of the finishing options. ^ as internal separator.
  Dim sqftidx = 14      ' Square feet of media used.
  Dim printidx = 15     ' Square feet printed on (e.g. sometimes signs are double-sided)
  Dim wasteidx = 16     ' Square feet of media wasted.
  Dim wastecostidx = 17 ' Cost of media wasted.
  Dim inkcostidx = 18   ' Cost of ink.
  ' Dim thickuomidx = 19   Units of measure for thickness (inches, mm, mil)
  
  ' ************************* CREATE FILE NAME WITH NEW FORMAT ****************************

  ' FinString will look like this, with flexible number of finishing options: CUS^AF-RC^AL-GL
  ' We want to put the STD or CUS into StdCus and put the rest into FinDetails
  dim FinString as string = Items(odescidx).ToString  
  dim FinArray() as string = FinString.split("^")
  dim FinStdCus as string = FinArray(0)
  dim FinDetails as string = FinString.substring(4, FinString.length - 4)

  dim duedate as String 
  dim servicetype as String ' will be STD, RUSH or HOT
  ' If service type is HOT, use 99 for the day. Otherwise use the day of the month of the ready date.
  szWork = Request("fservicetype").Substring(0,1)
  if szWork = "H" then
    duedate = "99"
    servicetype = "HOT"
  else
    j = Request("readydate").IndexOf(" ")
    If j >= 0 then
      ' Ready date will be format mm/dd/YYYY. Include just the dd.
      szWork = Request("readydate").Substring(0,j)
      duedate = CDate(szWork).ToString("dd") 
      szWork = Request("fservicetype").Substring(0,1) 
      If szWork = "R"
        servicetype = "RUSH"
      Else If szWork = "S"
        servicetype = "STA"
      End If
    Else  'this should never happen
      duedate = "??"
      servicetype = "???"
    End If
  end if

  ' TO DO: Change underscores to ~'s which are less likely to be included in user's filenames.
  ' Note to self: & is concatenate. _ allows us to continue a command across a line break.
  ' Len has asked for a space after the account name, to deal with an issue with the Onyx.
  dim szNewFileName as String = gblCompleteDir & _ 
                               Items(prodidx).ToString   _
		      & "_" &  Items(lamidx).ToString    _
		      & "_" &  FinStdCus.ToString        _
		      & "_" &  Items(heightidx).ToString _
		      & "x" &  Items(widthidx).ToString  _
		      & "_Q" & Items(quanidx).ToString   _
		      & "_D" & duedate.ToString          _  
		      & "_" &  servicetype.ToString      _  
		      & "_" &  deliverytype.ToString     _  
		      & "_" &  city.ToString             _  
		      & "_" &  Items(notesidx).ToString  _
		      & "_" &  accountname.ToString      _
		      & " " &  Items(layeridx).ToString  _
		      & "_" &  FinDetails.ToString       _
		      & "_P" & Items(pagesidx).ToString  _
		      & "_"

  ' Leave this out till we are ready to populate thickness and remove thicknesses from product names:
                    ' Items(thickidx).ToString  & "_" &_
                    ' Items(uomthickidx).ToString  & "_" &_

  ' append customer file name, keeping total file name below 250 chars long
  dim customerfilename as String
  customerfilename = finew.Name.Substring(0, IIF (finew.Name.LastIndexOf(".") > 0, finew.Name.LastIndexOf("."), finew.Name.Length))
  Dim namelength as Integer
  Dim subtractchars as Integer
  Dim lastchar as Integer
  Dim shorterfilename as String
  namelength = szNewFileName.Length + customerfilename.Length + szExt.Length
  if (namelength >= 250)
    subtractchars = namelength - 250
    if (subtractchars < customerfilename.Length)
      lastchar = customerfilename.Length - subtractchars
      shorterfilename = customerfilename.Substring(0, lastchar)
      szNewFileName &= shorterfilename
    end if ' else scenario is that we don't add customerfilename into szNewFileName
  else
    szNewFileName &= customerfilename
  end if

  ' ************************* RENAME THE FILE ****************************
  ' rename file; if destination file name already exists, append (#) to file name before the file extension
  finew = Nothing
  finew = New FileInfo(szNewFileName & szExt)
  j = 0
  Do while finew.Exists
    finew = nothing
    j += 1  
    finew = New FileInfo(szNewFileName & "(" & j.ToString & ")" & szExt)
  Loop
  fi.MoveTo(finew.FullName) 
          
End Sub

Private Sub ProcessShippingDoc(fi)
    Dim szFile as string = gblPackListDir & Request("Token")
    Dim szExt as string = ".pdf"
    Dim j as Integer
    Dim finew as FileInfo

    Response.Write("szFile: " & szFile & "<br>")
    
    finew = New FileInfo(szFile & szExt)
    j = 0
    Do while finew.Exists
      finew = nothing
      j += 1  
      finew = New FileInfo(szFile & "(" & j.ToString & ")" & szExt)
    Loop
    fi.MoveTo(finew.FullName) 
      Response.Write("New File: " & finew.FullName & "<br>")

End Sub

</script>
