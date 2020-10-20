# GraduationTask  

# クラウドでの出欠、入退室管理
***物理的なセキュリティの入退室管理ではなく、出欠確認とその催促メール***  

<br />  

# メンバー
n19001 石垣　諒太  
n19003 儀間　拳斗  
n19008 瀬名波　稜太 

<br />  

# 概要  
出欠,入退室管理ソフトを個人塾や集会を開いている人向けに作ります。  
（メインが出欠でサブに入退出がある。）  
塾であれば、出席確認後親宛てにメールを送る。出席確認が取れていなければ出欠報告の催促メールを送る。  
集会であれば、急なドタキャンにも対応できて運営しやすくなる。

<br />  

## 仕様書  
各教室、ブースなどに打刻用の管理者端末があり、その端末で打刻をしているので管理者アカウントにユーザーの入退室、出欠の編集ができる権限を与える。  
webから欠席の申請だけ許可する。（不正な出席を防ぐため）  
出欠の確認が取れていない人にテンプレメールを送付する。（出欠の催促メール）  
出席している人の入退出は取り、欠席の人は当然取らない  

<br />  

### メリット  
当日出席する人の出欠状況が常に把握できるので塾だったら先生、集会だったら主催者が運営しやすくなる。

<br />  

### 必要な工数
jsp、サーブレット、spring、DB、jbdc、サーバーの構築  

<br />

# クラウドで管理するもの
* データベース  
* http(Apache)  
* Appサーバー  
<br />  
 


## 予定表



| | 8 | 9 | 10 | 11 | 12 | 1 |
| ---- | ---- | ---- | ---- | ---- | ---- | ---- |
| 勉強期間 | ● | ● |  |  |  |  |
| 設計の見直し |  | ● |  |  |  |  |
| DB |  | ■ | ● |  |  |  |
| App |  | ■ | ● |  |  |  |
| 構築 |  |  |  | □ |  |  |
| テスト |  |  |  | ■ | □ |  |
| 追加機能 |  |  |  | ● |  |  |
|  |  |  |  |  |  |  |

●：月全体　□：前半　■：後半

移植元作成中  
11/11 -> awsにテスト移植


<br />
