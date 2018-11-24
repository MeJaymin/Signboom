<script runat="server">
'********************************************************************************
'
'   This class will read, parse and optionally delete the upload summary file
'   created by ajmUpload.  Instantiate it with the location of the summary
'   file (i.e.)
'
'       Dim UL as ULResult = new UlResult(szSummaryFile, ULResult.FileDisposition.Retain)
'
'   The class will read the summary file and parse its contents to create a set
'   of properties that describe the uploaded files.
'
'   *** IMPORTANT ***
'
'   The code that deletes the summary file is commented out.  Please read the 
'   disclaimer below and understand the risks involved in deleting files base
'   on input that originates via the internet.
'
'********************************************************************************
Public Class ULResult

    ' Enumeration for Class Constructor
    Public Enum FileDisposition
        Delete = 1
        Retain = 2
    End Enum

    ' A file descriptor is created for each file uploaded
    Public Structure UlFileDescription
        Public Name As String
        Public SourceName As String
        Public Length As Long
        Public UlStartTime As DateTime
        Public UlEndTime As DateTime
    End Structure


    Private mToken As String
    Private mSessionEnd As DateTime
    Private mUploadStartTime As DateTime
    Private mUploadStopTime As DateTime
    Private mIPAddr As String
    Private mTotalLength As Long
    Private mHeaderLength As Integer
    Private mReceivedLength As Long
    Private mErrorMsg As String
    Private mFiles As New Collection

    'Upload Token
    ReadOnly Property Token() As String
        Get
            Return mToken
        End Get
    End Property
    'Date and Time the upload session ended
    ReadOnly Property SessionEnd() As DateTime
        Get
            Return mSessionEnd
        End Get
    End Property
    'Date and Time the upload session started
    ReadOnly Property UploadStartTime() As DateTime
        Get
            Return mUploadStartTime
        End Get
    End Property
    'Date and Time the last file was received
    ReadOnly Property UploadStopTime() As DateTime
        Get
            Return mUploadStopTime
        End Get
    End Property
    'Uploader IP Address
    ReadOnly Property IPAddr() As String
        Get
            Return mIPAddr
        End Get
    End Property
    'Total expected length (including headers) of the upload stream
    ReadOnly Property TotalLength() As Long
        Get
            Return mTotalLength
        End Get
    End Property
    'Total length of headers in the upload stream
    ReadOnly Property HeaderLength() As Integer
        Get
            Return mHeaderLength
        End Get
    End Property
    'Total length of data received during the upload session
    ReadOnly Property ReceivedLength() As Long
        Get
            Return mReceivedLength
        End Get
    End Property
    'Collection of UlFileDescription Structures
    ReadOnly Property Files() As Collection
        Get
            Return mFiles
        End Get
    End Property
    'Error Message
    ReadOnly Property ErrorMsg() As String
        Get
            Return mErrorMsg
        End Get
    End Property

    Public Sub New(ByVal szFile As String, Optional ByVal iDisposition As FileDisposition = FileDisposition.Retain)

        'Load file 

        If Not LoadItems(szFile) Then Return


        mToken = System.IO.Path.GetFileNameWithoutExtension(szFile)

        'Uncomment the short routine below to remove the Summary
        'file after it's been read and processed.  
        '*** IMPORTANT ***
        ' PLEASE READ THE FOLLOWING DISCLAIMER
        ' Deleting a file based on input received from a web page is inherently
        ' risky and may pose a security threat in your environment.  Please ensure 
        ' that all necessary precautions and /or validations are executed before
        ' deleting any files.  Use this software at your own risk.

		If iDisposition = FileDisposition.delete then
	        Try
    	        System.IO.File.Delete(szFile)
        	Catch ex As Exception
            	' add custom error processing here.
		   mErrorMsg = ex.Message
		End Try
		End If

    End Sub

    Private Function LoadItems(ByVal szFile As String) As Boolean
        Dim szItems As String
        Dim UF As New UlFileDescription
        Dim sr As System.IO.StreamReader

        ' Make sure the file exists
        Dim fi As New System.IO.FileInfo(szFile)
        If Not fi.Exists Then
            'If we can find it, set up an appropriate error message and exit
            mErrorMsg = "Upload Summary File Not Found"
            Return False
        End If
        '16384 is a reasonable maximum length - if the length
        ' is longer, we've probably got a bad file
        If fi.Length > 16384 Then
            mErrorMsg = "Upload Summary File is Too Long"
            Return False
        End If

        ' Read and parse each line item.  Line items have the format
        '
        '   key:value
        '
        ' Abort on any error

        Try
            sr = New System.IO.StreamReader(szFile)
            szItems = sr.ReadLine
            Do While szItems <> ""
                Dim j As Integer = szItems.IndexOf(":")
                If j >= 0 Then
                    ' Initialize the appropriate propertiies
                    Dim sz As String = szItems.Substring(0, j)
                    Select Case sz
                        Case "Date"
                            mSessionEnd = FixDate(szItems.Substring(j + 1))
                        Case "Start"
                            mUploadStartTime = FixDate(szItems.Substring(j + 1))
                        Case "Stop"
                            mUploadStopTime = FixDate(szItems.Substring(j + 1))
                        Case "IP"
                            mIPAddr = szItems.Substring(j + 1)
                        Case "ExpectedLength"
                            mTotalLength = CLng(szItems.Substring(j + 1))
                        Case "HeaderLength"
                            mHeaderLength = CInt(szItems.Substring(j + 1))
                        Case "ReceivedLength"
                            mReceivedLength = CLng(szItems.Substring(j + 1))
                            ' This SELECT statement requires that all File
                            ' related data is written consecutively ending
                            ' with 'FileStop'
                        Case "File"
                            UF.Name = szItems.Substring(j + 1)
                        Case "SourceFile"
                            UF.SourceName = szItems.Substring(j + 1)
                        Case "FileLen"
                            UF.Length = CLng(szItems.Substring(j + 1))
                        Case "FileStart"
                            UF.UlStartTime = FixDate(szItems.Substring(j + 1))
                        Case "FileStop"
                            UF.UlEndTime = FixDate(szItems.Substring(j + 1))
                            mFiles.Add(UF)
                            'Reinitialize all File parameters
                            UF.Name = ""
                            UF.Length = 0
                            UF.UlStartTime = #12:00:00 AM#
                            UF.UlEndTime = #12:00:00 AM#
                    End Select
                End If
                szItems = sr.ReadLine
            Loop
        Catch ex As Exception
            mErrorMsg = "Error Accessing Summary File: " & szFile & ControlChars.CrLf & ex.Message
            Return False
        Finally
            If Not (sr Is Nothing) Then sr.Close()
        End Try
        Return True

    End Function

    Private Function FixDate(szDate as String) as DateTime

	return CDate(szDate)

	dim i as integer
        dim mo as string
        dim dy as string

	i = szDate.IndexOf("/")
        dy = szDate.Substring(0, i)
        szDate = szDate.Substring(i+1)

	i = szDate.IndexOf("/")
        mo = szDate.Substring(0, i)
        szDate = szDate.Substring(i)
	szDate = mo & "/" & dy & szDate
	return CDate(szDate)

    End Function

End Class
</script>
