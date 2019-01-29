SET QUOTED_IDENTIFIER ON;

IF NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[#__imageshow_external_source_instagram]') AND type in (N'U'))
BEGIN
CREATE TABLE [#__imageshow_external_source_instagram](
	[external_source_id] [int] IDENTITY(1,1) NOT NULL,
    [external_source_profile_title] [nvarchar](255) NULL,
    [instagram_app_id] [nvarchar](255) NULL,
    [instagram_secret] [nvarchar](255) NULL,
    [instagram_callback_url] [nvarchar](500) NULL,
    [instagram_access_token] [nvarchar](255) NULL,
    [instagram_current_user_id] [nvarchar](255) NULL,
    [instagram_find_user] [nvarchar](255) NULL,
    [instagram_find_hashtag] [nvarchar](255) NULL,
CONSTRAINT [PK_#__imageshow_external_source_instagram_external_source_id] PRIMARY KEY CLUSTERED
(
	[external_source_id] ASC
)WITH (STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF)
)
END;